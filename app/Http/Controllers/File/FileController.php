<?php


namespace App\Http\Controllers\File;


use App\Http\Controllers\Controller;
use App\Http\Requests\File\FileStoreRequest;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $storeFilename = \Storage::disk('file_store')->putFile('/', $file);

        if($storeFilename === false) {
            return response()->json(['error' => 'File upload error'], 500);
        }

        $model = File::create([
            'name' => $file->getClientOriginalName(),
            'filepath' => $storeFilename,
            'disk' => 'file_store',
            'type' => '1 FIRMWARE',
            'size' => $file->getSize()
        ]);

        return new JsonResource($model);
    }

    public function download(File $file) {
        return \Storage::disk('file_store')->download($file->filepath);
    }
}
