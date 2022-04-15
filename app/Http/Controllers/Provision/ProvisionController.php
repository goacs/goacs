<?php

namespace App\Http\Controllers\Provision;

use App\Http\Controllers\Controller;
use App\Http\Resource\Provision\ProvisionResource;
use App\Models\Device;
use App\Models\Provision;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProvisionController extends Controller
{
    public function index(Request $request) {
        $query = QueryBuilder::for(Provision::class, $request)
            ->allowedFilters([
                'id',
                'name',
                'updated_at',
            ]);
        return $query->paginate($request->per_page ?: 25);
    }

    public function show(Provision $provision) {
        return new ProvisionResource($provision);
    }
}
