<?php


namespace App\Http\Controllers\File;


use App\Http\Controllers\Controller;
use App\Http\Requests\File\FileStoreRequest;
use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['download']);
    }

    public function index(Request $request) {
        $query = File::query();
        $this->prepareFilter($request, $query);
        return $query->paginate(25);
    }

    public function show(Request $request, File $file) {

    }

    public function store(FileStoreRequest $request) {
        $file = $request->file('file');
        \Storage::disk('file_store')->putFile('/',$file);

    }

    public function download() {

    }
}
