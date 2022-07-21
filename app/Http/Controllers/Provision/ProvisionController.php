<?php

namespace App\Http\Controllers\Provision;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provision\ProvisionStoreRequest;
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

    public function store(ProvisionStoreRequest $request) {
        return \DB::transaction(function() use ($request) {
            $provision = Provision::create($request->except(['rules','denied']));
            $this->saveRelated($provision, $request);
            return new ProvisionResource($provision);
        });
    }

    public function update(Provision $provision, ProvisionStoreRequest $request) {
        return \DB::transaction(function() use ($request, $provision) {

            $provision->forceFill($request->except(['rules', 'denied']));
            $provision->save();

            $this->deleteRelated($provision);
            $this->saveRelated($provision, $request);

            return new ProvisionResource($provision->refresh());
        });
    }

    public function clone(Provision $provision) {
        return \DB::transaction(function () use ($provision){
            $clone = $provision->clone();
            $clone->name .= ' cloned '.now()->toDateTimeString();
            $clone->save();
            return new ProvisionResource($clone->refresh());
        });
    }

    public function destroy(Provision $provision) {
        $this->deleteRelated($provision);
        $provision->delete();
    }

    private function deleteRelated(Provision $provision) {
        $provision->rules()->forceDelete();
        $provision->denied()->forceDelete();
    }

    private function saveRelated(Provision $provision, Request $request) {
        foreach ($request->input('rules', []) as $rule) {
            $provision->rules()->create(collect($rule)->except(['uniq'])->toArray());
        }

        foreach ($request->input('denied', []) as $denied) {
            $provision->denied()->create(collect($denied)->except(['uniq'])->toArray());
        }
    }
}
