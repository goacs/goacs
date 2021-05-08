<?php

declare(strict_types=1);


namespace App\Http\Controllers\Settings;


use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\DebugStoreRequest;
use App\Models\Device;
use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class DebugController extends Controller
{
    public function index() {
        return $this->getDefaultResource();
    }

    public function store(DebugStoreRequest $request) {
        Setting::setValue('conversation_log', $request->debug);
        Device::whereNotIn('id', $request->devices)->update(['debug' => false]);
        Device::whereIn('id', $request->devices)->update(['debug' => true]);

        return $this->getDefaultResource();
    }

    public function getDefaultResource() {
        $debug = (bool) Setting::getValue('conversation_log');
        $debugDevices = Device::whereDebug(true)->get();

        return JsonResource::make([
            'debug' => $debug,
            'devices' => $debugDevices
        ]);
    }
}
