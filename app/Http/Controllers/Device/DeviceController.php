<?php


namespace App\Http\Controllers\Device;


use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceUpdateRequest;
use App\Http\Resource\Device\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $query = Device::query();
        $this->prepareFilter($request, $query);
        return $query->paginate(25);
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