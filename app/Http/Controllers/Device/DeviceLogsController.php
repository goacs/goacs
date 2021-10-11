<?php


namespace App\Http\Controllers\Device;


use App\ACS\Context;
use App\ACS\Types;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceAddObjectRequest;
use App\Http\Requests\Device\DeviceUpdateRequest;
use App\Http\Resource\Device\DeviceResource;
use App\Models\Device;
use App\Models\Log;
use App\Models\Task;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DeviceLogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Device $device, Request $request) {
        $query = QueryBuilder::for(Log::class, $request)
            ->where('device_id', $device->id)
            ->orderByDesc('created_at')
            ->allowedFilters([
                'code',
                'message',
                'type',
                'from',
                AllowedFilter::scope('created_after')
            ]);
        return $query->paginate($request->per_page ?: 25);
    }
}
