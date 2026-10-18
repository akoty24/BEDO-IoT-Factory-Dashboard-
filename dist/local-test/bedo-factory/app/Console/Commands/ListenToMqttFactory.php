<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class ListenToMqttFactory extends Command
{
    protected $signature = 'mqtt:listen-factory';
    protected $description = 'Listen to factory sensors and data';

    public function handle()
    {
        $this->info('Listening to MQTT factory topics...');

        // 1. استدعاء الاتصال الفعلي أولاً
        $mqtt = MQTT::connection();

        // 2. الاشتراك في الـ Topic المطلوب
        $mqtt->subscribe(config('factory.mqtt.root').'/#', function (string $topic, string $message) {
            $this->info("Received -> Topic: [{$topic}] | Message: {$message}");
        }, 0);

        // 3. تشغيل حلقة الاستماع الباقية (Loop) لكي يظل الأمر مستምعاً ولا ينتهي
        $mqtt->loop(true);
    }
}