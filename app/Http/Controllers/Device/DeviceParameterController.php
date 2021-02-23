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
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request, Device $device) {
        $query = $device->parameters();
        $this->prepareFilter($request, $query);
        return $query->paginate(25);
    }

    public function show(Device $device, DeviceParameter $parameter) {
        return new DeviceParameterResource($parameter);
    }

    public function store(Device $device, DeviceParameter $parameter, DeviceParameterStoreRequest $request) {
        $parameter->fill($request->validated())->save();
        return new DeviceParameterResource($parameter);
    }

    public function update(Device $device, DeviceParameter $parameter, DeviceParameterStoreRequest $request) {
        $parameter->fill($request->validated())->save();
        return new DeviceParameterResource($parameter);
    }
}
