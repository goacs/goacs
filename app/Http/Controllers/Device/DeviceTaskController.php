<?php


namespace App\Http\Controllers\Device;


use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Device $device) {
        return new JsonResource($device->tasks);
    }
}
