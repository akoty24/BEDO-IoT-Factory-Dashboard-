<?php

/*
|--------------------------------------------------------------------------
| BEDO IoT Factory System
|--------------------------------------------------------------------------
|
| Dashboard definition. Topics, output names and input names match the
| trainer program on the Raspberry Pi (mqtt_client.py / MQTT_TOPICS.md),
| so a name here must be the same name the trainer uses on MQTT.
|
*/

return [

    'name' => 'BEDO IOT Factory System',

    'version' => '3.0.0',

    /*
    |--------------------------------------------------------------------------
    | MQTT  –  own Mosquitto broker on the Raspberry Pi
    |--------------------------------------------------------------------------
    | The browser connects over WebSockets (port 9001) as user "web".
    | Set these in .env:
    |     MQTT_WS_URL=ws://<raspberry-pi-ip>:9001   (leave empty on the Pi)
    |     MQTT_USERNAME=web
    |     MQTT_PASSWORD=<password of "web" from mosquitto/setup_mosquitto.sh>
    |
    | Note: the browser needs the "web" user's password, so it is visible
    | in the page source. Give "web" only the topic rights it needs.
    */

    'mqtt' => [
        // Empty → ws://<host that served the page>:9001 (dashboard on the Pi).
        'ws_url' => env('MQTT_WS_URL', ''),
        'username' => env('MQTT_USERNAME', 'web'),
        'password' => env('MQTT_PASSWORD', ''),
        'root' => env('MQTT_TOPIC_PREFIX', 'factory'),
        'qos' => 1, // same as the trainer (QOS = 1)
    ],

    // ms to wait for factory/reply after START / STOP before "No reply".
    'reply_timeout' => 5000,

    // The trainer stops camera pictures CAMERA_STREAM_S (60 s) after the
    // last "on", so the page sends "on" again this often while watching.
    'camera_renew_ms' => 45000,

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    | Items with "route" open a page. Items with "command" are machine
    | buttons:  start → factory/cmd/step "home",  stop → factory/cmd/stop.
    */

    'pages' => [
        ['route' => 'factory.dashboard', 'label' => 'Dashboard', 'icon' => '📊', 'variant' => 'amber'],
        ['command' => 'start', 'label' => 'Start Homing', 'icon' => '🏠', 'variant' => 'green'],
        ['route' => 'factory.manual', 'label' => 'Manual Mode', 'icon' => '🔧', 'variant' => 'amber'],
        ['route' => 'factory.automatic', 'label' => 'Automatic Mode', 'icon' => '⚙️', 'variant' => 'amber'],
        ['command' => 'stop', 'label' => 'Stop Process', 'icon' => '⏹️', 'variant' => 'red'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Manual steps  →  factory/cmd/step   (trainer STEPS)
    |--------------------------------------------------------------------------
    */

    'steps' => [
        'home' => 'Home Sequence',
        'loading' => 'Loading Sequence',
        'drill' => 'Drill Sequence',
        'fibre' => 'Fiber Sequence',
        'sorting' => 'Sorting Sequence',
    ],

    /*
    |--------------------------------------------------------------------------
    | Outputs  →  factory/cmd/output {"name": ..., "value": 0/1}
    |             state from factory/outputs      (trainer OUTPUT_NAMES)
    |--------------------------------------------------------------------------
    */

    'outputs' => [
        'conveyor' => 'M.Conveyor',
        'drill_motor' => 'M.Drill Motor',
        'stepper1_run' => 'Stepper 1 Run',
        'stepper1_dir' => 'Stepper 1 DIR',
        'stepper2_run' => 'Stepper 2 Run',
        'stepper2_dir' => 'Stepper 2 DIR',
        'loading_valve' => 'Loading Valve',
        'drilling_valve' => 'Drilling Valve',
        'fiber_valve' => 'Fiber Valve',
        'chute1_valve' => 'Solid Metal Valve',
        'chute2_valve' => 'Drilled Metal Valve',
        'chute3_valve' => 'Solid Plastic Valve',
        'light_red' => 'Alarm',
        'light_yellow' => 'Warning',
        'light_green' => 'Run',
        'buzzer' => 'Buzzer',
    ],

    /*
    |--------------------------------------------------------------------------
    | Inputs (sensors, read only)  ←  factory/inputs   (trainer INPUT_NAMES)
    |--------------------------------------------------------------------------
    */

    'inputs' => [
        'magazine' => 'Work piece Detection',
        'metal_sensor' => 'Metal Detection',
        'capacitive1' => 'First Capacitive',
        'capacitive2' => 'Second Capacitive',
        'capacitive3' => 'Third Capacitive',
        'fiber_sensor' => 'Fiber Sensor',
        'storage_product1' => 'Solid Metal Detection',
        'storage_product2' => 'Drilled Metal Detection',
        'storage_product3' => 'Solid Plastic Detection',
        'storage_rejected' => 'Rejected Detection',
        'drill_up_hard' => 'High Hard Limit Drill',
        'drill_up_soft' => 'High Soft Limit Drill',
        'drill_down_soft' => 'Low Soft Limit Drill',
        'drill_down_hard' => 'Low Hard Limit Drill',
        'fiber_up_hard' => 'High Hard Limit Fiber',
        'fiber_up_soft' => 'High Soft Limit Fiber',
        'fiber_down_soft' => 'Low Soft Limit Fiber',
        'fiber_down_hard' => 'Low Hard Limit Fiber',
        'loading_forward' => 'Front Read Switch (Loading)',
        'loading_back' => 'Back Read Switch (Loading)',
        'drill_clamp_open' => 'Read Switch 1 (Drill)',
        'drill_clamp_closed' => 'Read Switch 2 (Drill)',
        'fiber_clamp_open' => 'Read Switch 1 (Fiber)',
        'fiber_clamp_closed' => 'Read Switch 2 (Fiber)',
        'chute1_forward' => 'Front Read Switch (Solid Metal)',
        'chute1_back' => 'Back Read Switch (Solid Metal)',
        'chute2_forward' => 'Front Read Switch (Drilled Metal)',
        'chute2_back' => 'Back Read Switch (Drilled Metal)',
        'chute3_forward' => 'Front Read Switch (Solid Plastic)',
        'chute3_back' => 'Back Read Switch (Solid Plastic)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer counters  ←  factory/counts
    |--------------------------------------------------------------------------
    */

    'counters' => [
        'product1' => ['label' => 'Product 1 (Red)', 'color' => 'text-emerald-400'],
        'product2' => ['label' => 'Product 2 (Orange)', 'color' => 'text-orange-400'],
        'product3' => ['label' => 'Product 3 (Yellow)', 'color' => 'text-amber-400'],
        'rejected' => ['label' => 'Rejected', 'color' => 'text-red-400'],
        'errors' => ['label' => 'Error Count', 'color' => 'text-red-400'],
        'warnings' => ['label' => 'Warnings', 'color' => 'text-amber-400'],
    ],

];
