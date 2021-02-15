<?php


namespace App\Http\Controllers\Device;


use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceUpdateRequest;
use App\Http\Resource\Device\DeviceResource;
use App\Models\Device;

class DeviceController extends Controller
{
    public function index() {
        return Device::paginate(25);
    }

    public function show(Device $device) {
        return new DeviceResource($device);
    }

    public function destroy(Device $device) {
        if($device->delete()) {
            return response()->json();
        }
        return  response()->json(null,500);
    }
}
