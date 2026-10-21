/* ==========================================================================
   BEDO IoT Factory System — Alpine.js component shared by every page.

   Talks to the trainer program on the Raspberry Pi (mqtt_client.py) through
   its Mosquitto broker. Every topic starts with config.root ("factory"):

   TRAINER → WEB (R = retained)
     online R  state R  outputs R  inputs R  encoder R  message R  part R
     part/finished  counts R  alarm R  alarm/event  camera/frame  reply
   WEB → TRAINER
     cmd/output {"name","value"}  cmd/step  cmd/auto  cmd/stop  cmd/pwm
     cmd/camera  cmd/refresh

   Config comes from App\View\Composers\FactoryLayoutComposer.
   Bundled by Vite through resources/js/app.js (no CDN: works offline).
   ========================================================================== */

import mqtt from 'mqtt';

// Header colours per line status. The status is derived from factory/state.
const STATUS_STYLES = {
    RUNNING:   { badge: 'bg-emerald-950 text-emerald-300', dot: 'bg-emerald-600 animate-pulse' },
    IDLE:      { badge: 'bg-slate-800 text-slate-200',     dot: 'bg-slate-500' },
    ALARM:     { badge: 'bg-red-950 text-red-300',         dot: 'bg-red-600 animate-pulse' },
    EMERGENCY: { badge: 'bg-red-950 text-red-300',         dot: 'bg-red-600 animate-pulse' },
    OFFLINE:   { badge: 'bg-gray-800 text-gray-400',       dot: 'bg-gray-500' },
    UNKNOWN:   { badge: 'bg-gray-800 text-gray-300',       dot: 'bg-gray-500' },
};
const PENDING_STYLE = { badge: 'bg-gray-900 text-white animate-pulse', dot: 'bg-gray-400 animate-pulse' };

// Sidebar machine buttons → trainer command (topic suffix + payload).
const MACHINE_COMMANDS = {
    start: { kind: 'step', payload: 'home' },
    stop:  { kind: 'stop', payload: 'stop' },
};

// Replies worth a toast even when OK (output toggles show up on the buttons).
const ANNOUNCED_REPLIES = ['step', 'auto', 'stop', 'pwm', 'refresh'];

const MAX_HISTORY = 15;

// Trainer values are 0/1, true/false, "on"/"off" … → boolean.
function flag(value) {
    if (typeof value === 'string') {
        return ['1', 'true', 'on', 'yes'].includes(value.trim().toLowerCase());
    }
    if (value && typeof value === 'object') return value.active === true;
    return Boolean(value);
}

function parseJson(text) {
    try {
        return JSON.parse(text);
    } catch (e) {
        return undefined;
    }
}

export function factoryDashboard(config) {
    const topic = (name) => `${config.root}/${name}`;

    return {
        currentTime: '',
        darkMode: document.documentElement.classList.contains('dark'),
        sidebarOpen: false, // phone / tablet drawer

        // Connection
        mqttClient: null,
        brokerStatus: 'Connecting...',
        trainerOnline: null, // factory/online: true / false / null (unknown yet)

        // Data from the trainer (null until the first, retained, message)
        machine: null,  // factory/state
        outputs: {},    // factory/outputs
        sensors: Object.fromEntries(config.inputs.map((name) => [name, false])), // factory/inputs
        encoder: null,  // factory/encoder
        message: '',    // factory/message
        part: null,     // factory/part
        counts: null,   // factory/counts (null until the trainer sends it)
        alarm: null,    // factory/alarm
        finishedParts: [], // factory/part/finished (newest first)
        alarmEvents: [],   // factory/alarm/event   (newest first)

        // START / STOP waiting for factory/reply
        pendingCommand: null, // 'start' | 'stop'
        pendingTimer: null,

        // Camera
        cameraOn: false,
        cameraUrl: null,
        cameraTimer: null,

        notice: null, // { message, type: 'info' | 'success' | 'error' }
        noticeTimer: null,

        init() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
            this.initMQTTConnection();
            window.addEventListener('beforeunload', () => {
                if (this.cameraOn) this.publishCommand('camera', 'off');
            });
        },

        updateTime() {
            this.currentTime = new Date().toLocaleTimeString('en-US', { hour12: false });
        },

        // Theme: initial value is set by the inline script in the layout <head>.
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            document.documentElement.classList.toggle('dark', this.darkMode);
            try {
                localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            } catch (e) {
                // Storage blocked (private mode): the theme still applies for this page.
            }
        },

        // ---------------- Derived state ----------------

        get connected() {
            return this.brokerStatus === 'Connected';
        },

        get lineStatus() {
            if (!this.connected || this.trainerOnline === false) return 'OFFLINE';
            if (!this.machine) return 'UNKNOWN';
            if (flag(this.machine.emergency)) return 'EMERGENCY';
            if (this.alarmActive || flag(this.machine.alarm)) return 'ALARM';
            if (flag(this.machine.running)) return 'RUNNING';
            return 'IDLE';
        },

        get machineStateLabel() {
            if (this.pendingCommand === 'start') return 'STARTING...';
            if (this.pendingCommand === 'stop') return 'STOPPING...';
            const status = this.lineStatus;
            if (status === 'RUNNING' && this.machine.step) {
                return `RUNNING · ${String(this.machine.step).toUpperCase()}`;
            }
            return status;
        },

        get machineStateStyle() {
            return this.pendingCommand ? PENDING_STYLE : STATUS_STYLES[this.lineStatus];
        },

        // REMOTE button on the trainer. null = not known yet.
        get remoteOn() {
            return this.machine ? flag(this.machine.remote) : null;
        },

        // Commands that move the machine need the broker and REMOTE ON.
        get canControl() {
            return this.connected && this.trainerOnline !== false && this.remoteOn !== false;
        },

        get pwmOn() {
            return this.machine ? flag(this.machine.pwm) : false;
        },

        get alarmActive() {
            return Boolean(this.alarm && this.alarm.active);
        },

        get encoderText() {
            const e = this.encoder;
            if (e === null || e === undefined) return '—';
            if (typeof e !== 'object') return String(e);
            const num = (v) => Number(v).toLocaleString('en-US', { maximumFractionDigits: 1 });
            const mm = e.mm ?? e.position_mm ?? e.position;
            const rev = e.rev ?? e.revolutions;
            const parts = [];
            if (rev !== undefined) parts.push(`${num(rev)} rev`);
            if (mm !== undefined) parts.push(`${num(mm)} mm`);
            return parts.length ? parts.join(' · ') : JSON.stringify(e);
        },

        // Badges on the Manual page tabs (phone / tablet).
        get activeOutputsCount() {
            return Object.values(this.outputs).filter(flag).length;
        },

        get activeSensorsCount() {
            return Object.values(this.sensors).filter(Boolean).length;
        },

        isActive(sensorId) {
            return Boolean(this.sensors[sensorId]);
        },

        isOutputOn(name) {
            return flag(this.outputs[name]);
        },

        isMachineCommandActive(kind) {
            if (kind === 'start') return this.lineStatus === 'RUNNING';
            if (kind === 'stop') return this.lineStatus === 'EMERGENCY';
            return false;
        },

        // For lists of key/value data from the trainer (part, alarm …).
        entries(obj) {
            return obj && typeof obj === 'object' ? Object.entries(obj) : [];
        },

        display(value) {
            if (value === null || value === undefined || value === '') return '—';
            if (typeof value === 'boolean') return value ? 'yes' : 'no';
            if (typeof value === 'object') return JSON.stringify(value);
            return String(value);
        },

        // ---------------- Commands (web → trainer) ----------------

        // Publishes factory/cmd/<kind>. Returns true when sent.
        publishCommand(kind, payload) {
            if (!this.mqttClient || !this.mqttClient.connected) {
                this.showNotice('MQTT Broker is not connected right now!', 'error');
                return false;
            }
            const text = typeof payload === 'string' ? payload : JSON.stringify(payload);
            this.mqttClient.publish(topic(`cmd/${kind}`), text, { qos: config.qos });
            console.log(`Command sent to [${topic(`cmd/${kind}`)}]:`, text);
            return true;
        },

        // Sidebar START / STOP: wait for factory/reply.
        sendMachineCommand(name) {
            const command = MACHINE_COMMANDS[name];
            if (!command || !this.publishCommand(command.kind, command.payload)) return;

            this.pendingCommand = name;
            clearTimeout(this.pendingTimer);
            this.pendingTimer = setTimeout(() => {
                this.pendingCommand = null;
                this.showNotice(`No reply from the trainer to ${name.toUpperCase()}.`, 'error');
            }, config.replyTimeout);
        },

        runStep(step) {
            this.publishCommand('step', step);
        },

        toggleOutput(name) {
            this.publishCommand('output', { name, value: this.isOutputOn(name) ? 0 : 1 });
        },

        autoCommand(action) { // 'start' | 'feed'
            this.publishCommand('auto', action);
        },

        togglePwm() {
            this.publishCommand('pwm', this.pwmOn ? 'off' : 'on');
        },

        refreshData() {
            this.publishCommand('refresh', 'refresh');
        },

        // Stop + acknowledge alarm. Always accepted by the trainer.
        acknowledgeAlarm() {
            this.sendMachineCommand('stop');
        },

        toggleCamera() {
            if (this.cameraOn) {
                this.stopCamera();
            } else {
                this.startCamera();
            }
        },

        startCamera() {
            if (!this.publishCommand('camera', 'on')) return;
            this.cameraOn = true;
            clearInterval(this.cameraTimer);
            // The trainer stops after CAMERA_STREAM_S: keep asking while watching.
            this.cameraTimer = setInterval(() => {
                if (this.mqttClient?.connected) this.publishCommand('camera', 'on');
            }, config.cameraRenewMs);
        },

        stopCamera() {
            clearInterval(this.cameraTimer);
            this.cameraOn = false;
            this.publishCommand('camera', 'off');
            if (this.cameraUrl) URL.revokeObjectURL(this.cameraUrl);
            this.cameraUrl = null;
        },

        showNotice(message, type = 'info') {
            this.notice = { message, type };
            clearTimeout(this.noticeTimer);
            this.noticeTimer = setTimeout(() => (this.notice = null), 5000);
        },

        // ---------------- MQTT ----------------

        initMQTTConnection() {
            // No MQTT_WS_URL → the broker on the machine that served this page
            // (dashboard and Mosquitto both run on the Raspberry Pi).
            const url = config.wsUrl || `ws://${window.location.hostname}:9001`;
            this.mqttClient = mqtt.connect(url, {
                username: config.username || undefined,
                password: config.password || undefined,
                clientId: `web-${Math.random().toString(16).slice(2, 10)}`,
                reconnectPeriod: 2000,
            });

            this.mqttClient.on('connect', () => {
                this.brokerStatus = 'Connected';
                console.log('Connected to MQTT broker via WebSockets!');
                this.mqttClient.subscribe(topic('#'), { qos: config.qos }, (err) => {
                    if (!err) console.log(`Subscribed to ${topic('#')}`);
                });
            });

            this.mqttClient.on('reconnect', () => {
                this.brokerStatus = 'Reconnecting...';
            });

            this.mqttClient.on('offline', () => {
                this.brokerStatus = 'Disconnected';
                this.pendingCommand = null;
                clearTimeout(this.pendingTimer);
            });

            this.mqttClient.on('error', (err) => {
                this.brokerStatus = 'Error';
                console.error('MQTT connection error: ', err);
                if (/not authorized|bad user name or password/i.test(err.message)) {
                    this.showNotice('MQTT login refused – check MQTT_USERNAME / MQTT_PASSWORD in .env', 'error');
                }
            });

            this.mqttClient.on('message', (t, payload) => this.handleMessage(t, payload));
        },

        handleMessage(fullTopic, payload) {
            const prefix = `${config.root}/`;
            if (!fullTopic.startsWith(prefix)) return;
            const name = fullTopic.slice(prefix.length);

            if (name.startsWith('cmd/')) return; // our own (or another page's) commands
            if (name === 'camera/frame') return this.showCameraFrame(payload);

            const text = payload.toString();
            if (name === 'online') {
                this.trainerOnline = text.trim().toLowerCase() === 'online';
                return;
            }
            if (name === 'message') {
                this.message = text;
                return;
            }

            const data = parseJson(text);
            if (data === undefined) {
                console.log('Raw MQTT Message:', fullTopic, text);
                return;
            }

            switch (name) {
                case 'state':
                    this.machine = data;
                    break;
                case 'outputs':
                    this.outputs = data;
                    break;
                case 'inputs':
                    for (const id of Object.keys(this.sensors)) {
                        if (id in data) this.sensors[id] = flag(data[id]);
                    }
                    break;
                case 'encoder':
                    this.encoder = data;
                    break;
                case 'part':
                    this.part = data;
                    break;
                case 'part/finished':
                    this.finishedParts = [data, ...this.finishedParts].slice(0, MAX_HISTORY);
                    break;
                case 'counts':
                    this.counts = data;
                    break;
                case 'alarm':
                    this.alarm = data;
                    break;
                case 'alarm/event':
                    this.alarmEvents = [data, ...this.alarmEvents].slice(0, MAX_HISTORY);
                    break;
                case 'reply':
                    this.handleReply(data);
                    break;
            }
        },

        // factory/reply: {time, command, payload, ok, info}
        handleReply(reply) {
            const pending = this.pendingCommand && MACHINE_COMMANDS[this.pendingCommand];
            if (pending && pending.kind === reply.command) {
                this.pendingCommand = null;
                clearTimeout(this.pendingTimer);
            }
            if (!reply.ok) {
                this.showNotice(`${reply.command}: ${reply.info}`, 'error');
            } else if (ANNOUNCED_REPLIES.includes(reply.command)) {
                this.showNotice(`${reply.command}: ${reply.info}`, 'success');
            }
        },

        showCameraFrame(payload) {
            if (!this.cameraOn) return;
            const url = URL.createObjectURL(new Blob([payload], { type: 'image/jpeg' }));
            if (this.cameraUrl) URL.revokeObjectURL(this.cameraUrl);
            this.cameraUrl = url;
        },
    };
}
