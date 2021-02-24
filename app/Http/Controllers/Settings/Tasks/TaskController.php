<?php


namespace App\Http\Controllers\Settings\Tasks;


use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Task\TaskStoreRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Psy\Util\Json;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $query = Task::where(['for_type' => Task::TYPE_GLOBAL]);
        $this->prepareFilter($request, $query);

        return $query->paginate(25);
    }

    public function show(Request $request, Task $task) {
        return new JsonResource($task);
    }

    public function store(TaskStoreRequest $request) {
        $task = new Task();
        $task->forceFill($request->validated());
        $task->save();
        return new JsonResource($task);

    }

    public function update(Task $task, TaskStoreRequest $request) {
        $task->forceFill($request->validated());
        $task->save();
        return new JsonResource($task);
    }
}
