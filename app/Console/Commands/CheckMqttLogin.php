<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use Throwable;

/**
 * Tries to log in to the broker with the dashboard's MQTT user, so the
 * installers can reject a wrong password before saving it.
 *
 * Exit codes: 0 = login OK, 2 = user/password refused, 3 = broker unreachable.
 */
class CheckMqttLogin extends Command
{
    public const LOGIN_REFUSED = 2;

    public const UNREACHABLE = 3;

    protected $signature = 'mqtt:check-login
        {--host= : Broker host (default: host of MQTT_WS_URL, else 127.0.0.1)}
        {--port=1883 : Broker MQTT port}
        {--username= : Default: MQTT_USERNAME}
        {--password= : Default: MQTT_PASSWORD}';

    protected $description = 'Check that the dashboard MQTT user can log in to the broker';

    public function handle(): int
    {
        $host = $this->option('host')
            ?: parse_url((string) config('factory.mqtt.ws_url'), PHP_URL_HOST)
            ?: '127.0.0.1';
        $port = (int) $this->option('port');
        $username = $this->option('username') ?? config('factory.mqtt.username');
        $password = $this->option('password') ?? config('factory.mqtt.password');

        $settings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setConnectTimeout(5);

        try {
            $client = new MqttClient($host, $port, 'web-check-'.bin2hex(random_bytes(4)));
            $client->connect($settings, true);
            $client->disconnect();
        } catch (Throwable $e) {
            if (preg_match('/unauthori[sz]ed|not authori[sz]ed|bad user ?name or password|identifier rejected/i', $e->getMessage())) {
                $this->error("Login refused for user '{$username}' on {$host}:{$port} - wrong password?");

                return self::LOGIN_REFUSED;
            }
            $this->warn("Cannot reach the broker at {$host}:{$port}: {$e->getMessage()}");

            return self::UNREACHABLE;
        }

        $this->info("MQTT login OK as '{$username}' on {$host}:{$port}.");

        return self::SUCCESS;
    }
}
