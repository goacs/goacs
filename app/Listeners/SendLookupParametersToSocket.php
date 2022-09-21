<?php


namespace App\Listeners;


use App\ACS\Events\ParameterLookupDone;

class SendLookupParametersToSocket
{
    public function handle(ParameterLookupDone $event) {
        $device = $event->device;
        $parameters = $event->parameters;
//        Websocket::broadcast()->to('device.'.$device->id)->emit(ParameterLookupDone::class, $parameters);
    }
}
