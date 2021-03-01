<?php


namespace App\Http\Controllers\Device;


use App\ACS\Context;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceParameterStoreRequest;
use App\Http\Resource\Device\DeviceParameterResource;
use App\Models\Device;
use App\Models\DeviceParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class DeviceParameterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request, Device $device) {
        $query = $device->parameters()->orderBy('name');
        $this->prepareFilter($request, $query);
        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate(25);
        /** @var ParameterValuesCollection $cachedItems */
        $cachedItems = \Cache::get(Context::LOOKUP_PARAMS_PREFIX.$device->serial_number, false);
        if($cachedItems) {
            $paginator->getCollection()->transform(function(DeviceParameter $parameter) use ($cachedItems) {
                $parameter->cached = $cachedItems->get($parameter->name)?->value;
                return $parameter;
            });
        }

        return (new JsonResource($paginator))->additional([
            'has_cached_items' => $cachedItems !== false
        ]);
    }

    public function cached(Request $request, Device $device) {
        $cachedItems = \Cache::get(Context::LOOKUP_PARAMS_PREFIX.$device->serial_number, []);
        return new JsonResource($cachedItems);
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
