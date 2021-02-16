<?php


namespace App\Http\Controllers\File;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['download']);
    }

    public function index(Request $request) {

    }

    public function download() {

    }
}
