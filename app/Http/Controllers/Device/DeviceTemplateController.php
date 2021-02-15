<?php


namespace App\Http\Controllers\Device;


use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceTemplateController extends Controller
{
    public function index(Device $device) {
        return new JsonResource($device->templates);
    }
}
