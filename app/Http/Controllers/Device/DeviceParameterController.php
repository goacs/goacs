<?php


namespace App\Http\Controllers\Device;


use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceParameterStoreRequest;
use App\Http\Resource\Device\DeviceParameterResource;
use App\Models\Device;
use App\Models\DeviceParameter;
use Illuminate\Http\Request;

class DeviceParameterController extends Controller
{
    public function index(Request $request, Device $device) {
        $query = $device->parameters();
        $this->prepareFilter($request, $query);
        return $query->paginate(25);
    }

    public function show(DeviceParameter $deviceParameter) {
        return new DeviceParameterResource($deviceParameter);
    }

    public function store(DeviceParameter $deviceParameter, DeviceParameterStoreRequest $request) {
        $deviceParameter->fill($request->validated())->save();
        return new DeviceParameterResource($deviceParameter);
    }

    public function update(DeviceParameter $deviceParameter, DeviceParameterStoreRequest $request) {
        $deviceParameter->fill($request->validated())->save();
        return new DeviceParameterResource($deviceParameter);
    }
}
