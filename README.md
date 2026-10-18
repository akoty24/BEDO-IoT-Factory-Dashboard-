<p align="center">
  <img src="public/images/bedo-logo.webp" alt="BEDO - Innovating Education" height="70">
</p>

<h1 align="center">BEDO IoT Factory Dashboard</h1>

<p align="center">
  Real-time web control panel for the BEDO smart-factory training line.<br>
  <b>Laravel 12</b> · <b>Alpine.js</b> · <b>Tailwind CSS 4</b> · <b>MQTT over WebSockets</b> · <b>Raspberry Pi</b>
</p>

---

## What it does

The training line (conveyor, drilling station, fibre station and three sorting chutes) is driven by a
**trainer program on a Raspberry Pi**. That program publishes everything it does on a
**Mosquitto MQTT broker** running on the same Pi.

This dashboard is the web front-end for it. Any phone, tablet or PC on the factory network opens it in a
browser and talks to the broker **directly over WebSockets**. It does not poll, and it needs no database.

| Monitor (live) | Control |
|---|---|
| 30 sensors (inputs) | 16 outputs: toggle on/off (manual mode) |
| Output states, line status, mode, step | Manual steps: home, loading, drill, fibre, sorting |
| Production counters, encoder position | Automatic mode: start / feed part |
| Current part, finished parts | **Stop / acknowledge alarm** (always available) |
| Active alarm + alarm history | Live camera pictures on demand |
| Trainer online/offline, REMOTE on/off | Refresh all data |

**Safety rules built into the UI**
- **STOP is never disabled**, not even while another command is waiting for a reply.
- Commands that move the machine are disabled unless the broker is connected, the trainer is online and
  **REMOTE** is switched on at the trainer. The trainer enforces this too.
- Every START/STOP waits for the trainer's reply (`factory/reply`). If no reply comes within 5 s,
  the page says so.

Other features: responsive layout (phone / tablet / desktop), dark mode, and **works without internet**
(all JS, CSS and fonts are bundled).

---

## How it works

```
 Phone / tablet / PC (browser)                  Raspberry Pi
 ┌────────────────────────────┐   HTTP :80    ┌──────────────────────────────────┐
 │  Dashboard (Alpine.js)     │ ◄──────────── │ nginx + PHP-FPM → Laravel        │
 │                            │               │  (serves the pages + settings)   │
 │  mqtt.js  ────────────────────── WS :9001 ─► Mosquitto broker                 │
 └────────────────────────────┘               │      ▲  MQTT :1883               │
                                              │      │                           │
                                              │ Trainer program (Python) ──► PLC │
                                              └──────────────────────────────────┘
```

Laravel only renders the pages and hands the MQTT settings to the browser. All live data and commands
go **browser ↔ Mosquitto**.

### MQTT topics (root `factory`)

| Direction | Topic | Payload |
|---|---|---|
| trainer → web (retained) | `factory/online` | `online` / `offline` |
| | `factory/state` | `{"mode","step","running","remote","alarm","emergency","pwm",…}` |
| | `factory/outputs` · `factory/inputs` | `{"conveyor":0,…}` · `{"magazine":1,…}` |
| | `factory/counts` | `{"product1","product2","product3","rejected","errors","warnings"}` |
| | `factory/encoder` · `factory/message` · `factory/part` · `factory/alarm` | JSON / text |
| trainer → web | `factory/part/finished` · `factory/alarm/event` · `factory/reply` · `factory/camera/frame` | JSON / JPEG |
| web → trainer | `factory/cmd/output` | `{"name":"conveyor","value":1}` |
| | `factory/cmd/step` | `home` \| `loading` \| `drill` \| `fibre` \| `sorting` |
| | `factory/cmd/auto` | `start` \| `feed` |
| | `factory/cmd/stop` · `factory/cmd/pwm` · `factory/cmd/camera` · `factory/cmd/refresh` | text |

The browser logs in as the MQTT user **`web`**. That user should only be allowed to read `factory/#` and write
`factory/cmd/#` (Mosquitto ACL). Its password ends up in the page, so never use the trainer's own `factory` user here.

---

## Run it on the Raspberry Pi

**Requirements:** Raspberry Pi OS **Bookworm** or newer (ships PHP 8.2), Mosquitto with the WebSocket
listener on port **9001** and the `web` user, and internet access **once** to install nginx and PHP.

### 1. Build the release (on a Windows PC with this repo)

```powershell
.\build-release.ps1
```

This creates:
- `dist\bedo-factory-<version>.tar.gz`: the package for the Pi
- `dist\bedo-factory-<version>.zip`: a portable Windows package (see below)

### 2. Copy it to the Pi

```powershell
scp dist\bedo-factory-3.0.0.tar.gz <user>@<pi-ip>:~
ssh <user>@<pi-ip>
```

Use the user name you chose in Raspberry Pi Imager. Bookworm has no default `pi` user. You can also copy
the file with a USB stick.

### 3. Install

```bash
tar -xzf bedo-factory-3.0.0.tar.gz
cd bedo-factory
sudo bash deploy/install-on-pi.sh
```

The script:
1. installs **nginx** and **PHP-FPM** if missing,
2. copies the app to `/var/www/bedo-factory`,
3. asks for the **MQTT password of user `web`**, **tests it against Mosquitto**, and asks again if it is wrong,
4. creates `.env` (production, no debug) and caches config, routes and views,
5. sets up the nginx site on port **80** and starts everything.

When it finishes:

```
Dashboard installed:  http://<pi-ip>/
```

Open that address from any device on the factory network. The page connects to the broker on the same Pi
automatically, so nothing changes if the Pi's IP changes. nginx starts on boot, so there is nothing to start by hand.

### Update to a new version

Build again on the PC, copy the new `.tar.gz`, then on the Pi:

```bash
rm -rf bedo-factory && tar -xzf bedo-factory-3.0.0.tar.gz && cd bedo-factory
sudo bash deploy/install-on-pi.sh
```

`.env` and the logs on the Pi are kept, and the password is not asked again.

### Troubleshooting

| Problem | Check |
|---|---|
| `apt` fails during install | The Pi has no internet. Connect it once and run the script again |
| Page does not open | `sudo systemctl status nginx php8.2-fpm` |
| Error 500 | `sudo tail -30 /var/www/bedo-factory/storage/logs/laravel-*.log` |
| Page opens but shows **OFFLINE** | Trainer not running (`factory/online` = `offline`), or broker not reachable on port 9001 |
| "MQTT login refused" | Wrong `web` password: `sudo nano /var/www/bedo-factory/.env`, then run the install script again |
| Test the login by hand | `cd /var/www/bedo-factory && php artisan mqtt:check-login --host=127.0.0.1` |

---

## Run it on a Windows laptop (no installation)

`dist\bedo-factory-<version>.zip` contains everything, **PHP included**.

1. Unzip it anywhere.
2. Double-click **`bedo-factory\start-windows.bat`**.
3. The first time, enter the Pi's IP and the `web` password (keyboard in **English**). The password is checked before it is saved.
4. The browser opens **http://127.0.0.1:8080**. Close the black window to stop.

The laptop must be on the same network as the Pi. To enter the settings again, delete `bedo-factory\.env`.

---

## Development

**Requirements:** PHP 8.2+, Composer, Node.js 20+.

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
```

Point the dashboard at the Pi in `.env`:

```dotenv
MQTT_WS_URL=ws://172.20.151.6:9001   # leave empty when the dashboard runs on the Pi itself
MQTT_USERNAME=web
MQTT_PASSWORD=...
MQTT_TOPIC_PREFIX=factory
```

Run it with two terminals:

```bash
php artisan serve     # http://127.0.0.1:8000
npm run dev           # rebuilds CSS/JS on every change
```

When you add new Tailwind classes, run `npm run build` (or keep `npm run dev` running). The CSS only contains
classes Vite has seen.

Other commands:

```bash
php artisan test                 # feature tests
php artisan mqtt:check-login     # check the MQTT user/password against the broker
.\run-release-local.ps1          # run the built release on this PC (http://127.0.0.1:8080)
```

### Project structure

```
config/factory.php                    ← everything the dashboard shows: pages, steps, outputs,
                                        inputs (sensors), counters, MQTT settings
app/Http/Controllers/FactoryController.php
app/View/Composers/FactoryLayoutComposer.php   ← shared data for header/sidebar/footer + browser config
app/Console/Commands/CheckMqttLogin.php        ← php artisan mqtt:check-login
resources/js/factory-dashboard.js     ← Alpine component: MQTT client, state, commands
resources/css/app.css                 ← Tailwind 4 (dark mode by class)
resources/views/
  layouts/factory.blade.php           ← page shell, alarm / REMOTE banners, notices
  partials/  header · sidebar · footer
  components/ nav-link · nav-command · command-button · sensors-panel
  factory/   dashboard · manual · automatic
deploy/
  install-on-pi.sh                    ← Raspberry Pi installer (nginx + PHP-FPM)
  nginx-bedo-factory.conf
  .env.production                     ← template for the Pi's .env
  start-windows.bat / .ps1            ← Windows launcher (portable PHP)
build-release.ps1                     ← builds dist\*.tar.gz (Pi) and dist\*.zip (Windows)
```

**To add a sensor or an output**, add one line to `inputs` or `outputs` in `config/factory.php`, using the
same name the trainer uses on MQTT. No view changes are needed.

---

<p align="center">© BEDO Innovating Education</p>
