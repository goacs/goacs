<?php


namespace App\Http\Controllers\File;


use App\Http\Controllers\Controller;
use App\Http\Requests\File\FileStoreRequest;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Response;
use Spatie\QueryBuilder\QueryBuilder;

class FileController extends Controller
{
    const FILE_STORE_NAME = 'public';

    public function __construct()
    {
        $this->middleware('auth:api')->except(['download']);
    }

    public function index(Request $request) {
        $query = QueryBuilder::for(File::class)
            ->allowedFilters([
                'name',
                'type',
                'created_at',
            ]);
        return $query->paginate(25);
    }

    public function show(Request $request, File $file) {

    }

    public function store(FileStoreRequest $request) {
        $file = $request->file('file');
        $storeFilename = \Storage::disk(self::FILE_STORE_NAME)->putFileAs('/', $file, $file->getClientOriginalName());

        if($storeFilename === false) {
            return response()->json(['error' => 'File upload error'], 500);
        }

        $model = File::create([
            'name' => $file->getClientOriginalName(),
            'filepath' => $storeFilename,
            'disk' => self::FILE_STORE_NAME,
            'type' => '1 FIRMWARE',
            'size' => $file->getSize()
        ]);

        return new JsonResource($model);
    }

    public function download(File $file) {
        return \Storage::disk(self::FILE_STORE_NAME)->download($file->filepath, $file->name);
    }

    public function destroy(File $file) {
        \Storage::disk(self::FILE_STORE_NAME)->delete($file->filepath);
        $file->delete();
    }
}
