<?php

namespace App\View\Composers;

use Illuminate\View\View;

/**
 * Shared data for the dashboard layout (header, sidebar, footer and the
 * browser-side MQTT client), so individual pages don't have to pass it.
 */
class FactoryLayoutComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'appName' => config('factory.name'),
            'version' => config('factory.version'),
            'pages' => config('factory.pages'),
            'counters' => config('factory.counters'),
            'clientConfig' => $this->clientConfig(),
        ]);
    }

    /**
     * Config handed to public/js/factory-dashboard.js.
     */
    private function clientConfig(): array
    {
        return [
            'wsUrl' => config('factory.mqtt.ws_url'),
            'username' => config('factory.mqtt.username'),
            'password' => config('factory.mqtt.password'),
            'root' => config('factory.mqtt.root'),
            'qos' => config('factory.mqtt.qos'),
            'replyTimeout' => config('factory.reply_timeout'),
            'cameraRenewMs' => config('factory.camera_renew_ms'),
            'inputs' => array_keys(config('factory.inputs')),
        ];
    }
}
