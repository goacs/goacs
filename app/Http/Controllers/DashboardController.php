<?php


namespace App\Http\Controllers;


use App\Models\Device;
use App\Models\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index() {
        return response()->json([
           'devices_count' => Device::count(),
           'informs_count' => Device::updatedLast24Hours()->count(),
           'faults_count' => Log::last24Hours()->count(),
           'faults' => [],
        ]);
    }
}
