<?php


namespace App\Http\Controllers\Device;


use App\ACS\Types;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceAddObjectRequest;
use App\Http\Requests\Device\DeviceUpdateRequest;
use App\Http\Resource\Device\DeviceResource;
use App\Models\Device;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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

    public function addObject(DeviceAddObjectRequest $request, Device $device) {
        $acsTask = new \App\ACS\Entities\Task(Types::AddObject);
        $acsTask->setPayload(['parameter' => $request->name]);
        $task = Task::fromACSTask($acsTask);
        $task->on_request = Types::EMPTY;
        $task->infinite = false;
        $task = $device->tasks()->save(
            $task
        );
        return new JsonResource($task);
    }
}
