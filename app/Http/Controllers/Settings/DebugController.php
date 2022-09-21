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
        Setting::setValue('conversation_log', $request->boolean('debug'));
        Setting::setValue('debug_new_devices', $request->boolean('debug_new_devices'));
        Device::whereNotIn('id', $request->devices)->update(['debug' => false]);
        Device::whereIn('id', $request->devices)->update(['debug' => true]);

        return $this->getDefaultResource();
    }

    public function getDefaultResource() {
        $debug = (bool) Setting::getValue('conversation_log');
        $debugNewDevices = (bool) Setting::getValue('debug_new_devices');
        $debugDevices = Device::whereDebug(true)->get();

        return JsonResource::make([
            'debug' => $debug,
            'debug_new_devices' => $debugNewDevices,
            'devices' => $debugDevices
        ]);
    }
}
