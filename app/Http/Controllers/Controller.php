<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function prepareFilter(Request $request, $query) {
        if(! $request->filter) {
            return;
        }

        (new Collection($request->filter))
            ->each(fn($item, $key) => $query->where($key, 'like', "%{$item}%"));
    }
}
