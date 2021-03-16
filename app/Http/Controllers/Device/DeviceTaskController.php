<?php


namespace App\Http\Controllers\Device;


use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Task\TaskStoreRequest;
use App\Models\Device;
use App\Models\Task;
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

    public function store(Device $device, TaskStoreRequest $request) {
      $task = $device->tasks()->make();
      $task->forceFill($request->validated());
      $task->save();
      return new JsonResource($task);
    }

    public function update(Device $device, Task $task, TaskStoreRequest $request) {
      $task->forceFill($request->validated());
      $task->save();
      return new JsonResource($task);
    }

    public function destroy(Device $device, Task $task) {
      $task->forceDelete();
      return new JsonResource([]);
    }
}
