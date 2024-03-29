<?php


namespace App\Http\Controllers\Device;


use App\ACS\Context;
use App\ACS\Entities\Flag;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceParameterStoreRequest;
use App\Http\Requests\Device\PatchParametersRequest;
use App\Http\Resource\Device\DeviceParameterResource;
use App\Models\Device;
use App\Models\DeviceParameter;
use App\Models\Filters\FlagFilter;
use App\Models\TemplateParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DeviceParameterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request, Device $device) {
        $query = QueryBuilder::for($device->parameters());
        $query->select('*');
        $query->addSelect(\DB::raw("'device' as source"));
        $query->allowedFilters([
            'name',
            'type',
            'value',
            AllowedFilter::custom('flags', new FlagFilter())
        ]);

        $templateQuery = QueryBuilder::for(
            TemplateParameter::whereHas('template', function($query) use ($device) {
                $query->whereIn('id', $device->templates->pluck('id'));
            })
        );
        $templateQuery->select('*');
        $templateQuery->addSelect(\DB::raw("'template' as source"));
        $templateQuery->allowedFilters([
            'name',
            'type',
            'value',
            AllowedFilter::custom('flags', new FlagFilter())
        ]);

        //Magic
        $query->union($templateQuery->getQuery());

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($request->per_page ?: 25);
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

    public function store(Device $device, DeviceParameterStoreRequest $request) {
        $parameter = $device->parameters()->make();
        $parameter->fill($request->validated())->save();
        return new DeviceParameterResource($parameter);
    }

    public function update(Device $device, DeviceParameter $parameter, DeviceParameterStoreRequest $request) {
        $parameter->fill($request->validated())->save();
        return new DeviceParameterResource($parameter);
    }

    public function destroy(Device $device, DeviceParameter $parameter) {
        $parameter->delete();
        return new JsonResource([]);
    }

    public function patchParameters(Device $device, Request $request) {
        $parameters = $request->get('parameters');
        $pvc = new ParameterValuesCollection();

        foreach($parameters as $parameter) {
            $pvs = new ParameterValueStruct();
            $pvs->name = $parameter['name'];
            $pvs->type = $parameter['type'];
            $pvs->value = $parameter['value'];
            $pvs->setFlag(Flag::fromString($parameter['flags']));
            $pvc->put($pvs->name, $pvs);
        }

        DeviceParameter::massUpdateOrInsert($device, $pvc);
    }
}
