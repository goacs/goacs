<?php


namespace App\Http\Controllers\Template;


use App\Http\Controllers\Controller;
use App\Http\Requests\Template\TemplateStoreRequest;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $query = Template::withCount('parameters');
        $this->prepareFilter($request, $query);
        return $query->paginate(25);
    }

    public function show(Template $template) {
        return new JsonResource($template);
    }

    public function store(TemplateStoreRequest $request) {
        $template = Template::create($request->validated());
        return new JsonResource($template);
    }
}
