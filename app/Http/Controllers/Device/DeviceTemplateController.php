<?php


namespace App\Http\Controllers\Device;


use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceTemplateStoreRequest;
use App\Models\Device;
use App\Models\Template;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Device $device) {
        return new JsonResource($device->templates);
    }

    public function store(DeviceTemplateStoreRequest $request, Device $device) {
        $device->templates()->detach($request->template_id);
        $device->templates()->attach($request->template_id, ['priority' => $request->priority]);
        return JsonResource::collection($device->templates);
    }

    public function destroy(Device $device, Template $template) {
        $device->templates()->detach($template->id);
        return JsonResource::collection($device->templates);
    }
}
