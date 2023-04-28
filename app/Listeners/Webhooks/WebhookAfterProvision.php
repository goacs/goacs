<?php
declare(strict_types=1);

namespace App\Listeners\Webhooks;

use App\ACS\Events\ProvisionDone;
use App\Models\Log;
use App\Models\Setting;
use GuzzleHttp\Exception\ClientException;

class WebhookAfterProvision extends Webhook
{
    public function handle(ProvisionDone $event) {
        $this->device = $event->device;
        $url = Setting::getValue('webhook_after_provision');
        if($url != '') {
            try {
                $this->callWebhook($url);
            }
            catch (ClientException $clientException) {
                Log::logInfoFromDevice($this->device, "WebhookAfterProvision exception", [
                    'http code' => $clientException->getCode(),
                    'message' => $clientException->getMessage(),
                ]);
            }
            catch (\Exception $exception) {}
        }
    }
}
