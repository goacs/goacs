<?php

declare(strict_types=1);


namespace App\Http\Controllers\Device;


use App\ACS\Context;
use App\ACS\Entities\Flag;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class DeviceCachedParametersController extends Controller
{
    public function index(Device $device, Request $request) {
        /** @var ParameterValuesCollection $cachedItems */
        $cachedItems = \Cache::get(Context::LOOKUP_PARAMS_PREFIX.$device->serial_number, new ParameterValuesCollection());
        $cachedItems = $cachedItems->filter(function(ParameterValueStruct $item) use ($request) {
            $i = 0;
            foreach($request->input('filter', []) as $key => $value) {
                $item = $item->{$key};

                if($key === 'flag') {
                    if($value === '{}') {
                        return true;
                    }
                    $item = $item->toString();
                    $valueFlag = Flag::fromArray(array_filter(json_decode($value, true), fn($flagValue) => $flagValue === true));
                    $value = $valueFlag->toString();
                }

                if(Str::contains(Str::lower($item), Str::lower($value))) {
                    $i++;
                }
            }
            return $i === count($request->input('filter', []));
        });

        $paginator = new LengthAwarePaginator($cachedItems->forPage($request->page, $request->per_page ?: 25)->values(), $cachedItems->count(), $request->per_page ?: 25);
        return new JsonResource($paginator->toArray());
    }
}
