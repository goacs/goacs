<?php


namespace App\Http\Controllers\Device;


use App\ACS\Context;
use App\ACS\Types;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceAddObjectRequest;
use App\Http\Requests\Device\DeviceUpdateRequest;
use App\Http\Resource\Device\DeviceResource;
use App\Models\Device;
use App\Models\Task;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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
                AllowedFilter::scope('created_after')
            ]);
        return $query->paginate($request->per_page ?: 25);
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

    public function provision(Device $device) {
        \Cache::put(Context::PROVISION_PREFIX.$device->serial_number, true, now()->addMinutes(5));
        return $this->kick($device);
    }

    public function kick(Device $device) {
        $client = new Client();
        $auth = [$device->connection_request_user, $device->connection_request_password];
        try {
            $response = $client->get($device->connection_request_url, ['auth' => $auth]);

            if (in_array($response->getStatusCode(), [200, 201])) {
                return new JsonResource([]);
            }
        } catch (ClientException $exception) {
            $auth[] = 'digest';
            $response = $client->get($device->connection_request_url, ['auth' => $auth]);

            if (in_array($response->getStatusCode(), [200, 201])) {
                return new JsonResource([]);
            }
        }
        return response()->json(['error' => 'Kick failed'], 503);
    }

    public function lookup(Device $device) {
        \Cache::put(Context::LOOKUP_PARAMS_ENABLED_PREFIX.$device->serial_number, true, now()->addMinutes(5));
        return $this->kick($device);
    }
}
