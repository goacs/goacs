<?php


namespace App\Http\Controllers\Device;


use App\ACS\Context;
use App\ACS\Kick;
use App\ACS\Types;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceAddObjectRequest;
use App\Http\Requests\Device\DeviceCreateRequest;
use App\Http\Requests\Device\DeviceUpdateRequest;
use App\Http\Resource\Device\DeviceResource;
use App\Models\Device;
use App\Models\Log;
use App\Models\Setting;
use App\Models\Task;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $query = QueryBuilder::for(Device::class, $request)
            ->allowedFilters([
                'id',
                'serial_number',
                'serial_alt',
                'software_version',
                'product_class',
                'model_name',
                'updated_at',
                AllowedFilter::scope('created_after')
            ]);
        return $query->paginate($request->per_page ?: 25);
    }

    public function show(Device $device) {
        return new DeviceResource($device);
    }

    public function store(DeviceCreateRequest $request) {
        $device = new Device();
        $device->fill($request->validated());
        $device->connection_request_url = '';
        $device->save();
        return new DeviceResource($device);
    }

    public function update(DeviceUpdateRequest $request, Device $device) {
        $device->fill($request->validated())->save();
        return new DeviceResource($device);
    }

    public function destroy(Device $device) {
        $this->clearCache($device);
        $device->templates()->sync([]);
        $device->parameters()->delete();
        $device->delete();
        return new JsonResource([]);
    }

    public function addObject(DeviceAddObjectRequest $request, Device $device) {
        $acsTask = new \App\ACS\Entities\Tasks\Task(Types::AddObject);
        $acsTask->setPayload(['parameter' => $request->name]);
        $task = Task::fromACSTask($acsTask);
        $task->on_request = Types::EMPTY;
        $task->infinite = false;
        $task = $device->tasks()->save(
            $task
        );
        return new JsonResource($task);
    }

    public function provision(Device $device) {
        \Cache::put(Context::PROVISION_PREFIX.$device->serial_number, true, now()->addMinutes(5));
        return $this->kick($device);
    }

    public function kick(Device $device) {
        Log::logInfoFromDevice($device, 'KICK REQUEST');
        $kickService = Kick::fromDevice($device);

        if($kickService->kick()) {
            Log::logInfoFromDevice($device, 'KICK SUCCESSFUL');
            return new JsonResponse([]);
        }

        Log::logInfoFromDevice($device, 'KICK FAILED');
        return response()->json(['error' => 'Kick failed'], 503);
    }

    public function lookup(Device $device) {
        \Cache::put(Context::LOOKUP_PARAMS_ENABLED_PREFIX.$device->serial_number, true, now()->addMinutes(5));
        return $this->kick($device);
    }

    public function clearCache(Device $device) {
        \Cache::forget("SESSID_".$device->serial_number);
        \Cache::forget(Context::LOOKUP_PARAMS_PREFIX.$device->serial_number);
        \Cache::forget(Context::PROVISION_PREFIX.$device->serial_number);
        \Cache::forget(Context::LOOKUP_PARAMS_ENABLED_PREFIX.$device->serial_number);
        return new JsonResponse([]);
    }
}
