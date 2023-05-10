<?php
declare(strict_types=1);

namespace App\Listeners\Webhooks;

use App\Models\Device;
use App\Models\Setting;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class Webhook
{
    protected Device $device;
    protected function callWebhook(string $url): ResponseInterface {
        $guzzle = new Client([
            'verify' => boolval(Setting::getValue('webhook_ssl_verify')),
            'timeout' => Setting::getValue('webhook_timeout'),
        ]);

        return $guzzle->post($url, [
            'json' => [
                'device' => $this->device->toArray(),
            ]
        ]);
    }
}
