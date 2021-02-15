<?php


namespace App\Http\Controllers;


use App\Models\Device;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index() {
        return response()->json([
           'devices_count' => Device::count(),
           'informs_count' => 0,
           'faults_count' => 0,
           'faults' => [],
        ]);
    }
}
