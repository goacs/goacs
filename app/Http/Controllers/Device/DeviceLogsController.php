<?php


namespace App\Http\Controllers\Device;


use App\ACS\Context;
use App\ACS\Types;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceAddObjectRequest;
use App\Http\Requests\Device\DeviceUpdateRequest;
use App\Http\Requests\Device\DownloadDeviceLogRequest;
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

    public function downloadLogs(Device $device, DownloadDeviceLogRequest $request) {
        $logs = Log::whereDeviceId($device->id)->whereSessionId($request->session_id)
            ->orderBy('created_at')
            ->get();

        $buffer = $logs->map(fn(Log $log) => $this->formatLogEntry($log))->join('');


        $filename = "goacs_session_log_{$request->session_id}.txt";

        if(\Storage::disk('logs')->put($filename, $buffer) === true) {
            return new JsonResource([
               'url' =>  \Storage::disk('logs')->url($filename)
            ]);
        }

        return response()->json([], 500);
    }

    private function formatLogEntry(Log $log): string {
        /**
         *[2022-07-12 13:32:23] ACS -> DEVICE | DEVICE -> ACS
         *
         * content....
         */

        $from = $log->from;
        $to = 'acs';

        if($from === 'acs') {
            $to = 'device';
        }

        $strLog = "[{$log->created_at->toDateTimeString('microsecond')}] {$from} -> {$to}".PHP_EOL;
        $strLog .= 'Message: '.$log->message.PHP_EOL;
        $strLog .= 'Body: '.PHP_EOL.$log->full_xml.PHP_EOL.PHP_EOL;

        return $strLog;
    }
}
