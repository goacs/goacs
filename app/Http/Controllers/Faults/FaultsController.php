<?php

declare(strict_types=1);


namespace App\Http\Controllers\Faults;


use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Log;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class FaultsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $query = QueryBuilder::for(Log::class, $request)
            ->orderByDesc('created_at')
            ->fault()
            ->with(['device' => function($query){
                    $query->select('id','serial_number');
                }
            ])
            ->allowedFilters([
                'id',
                'device_id',
                'code',
                'message',
                AllowedFilter::scope('created_after')
            ]);
        return $query->paginate($request->per_page ?: 25);
    }
}
