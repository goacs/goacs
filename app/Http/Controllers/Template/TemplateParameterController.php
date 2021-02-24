<?php


namespace App\Http\Controllers\Template;


use App\Http\Controllers\Controller;
use App\Http\Requests\Template\TemplateParameterStoreRequest;
use App\Models\Template;
use App\Models\TemplateParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateParameterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request, Template $template) {
        $query = $template->parameters()->orderBy('name');
        $this->prepareFilter($request, $query);
        return $query->paginate(25);
    }

    public function show(Template $template, TemplateParameter $parameter) {
        return new JsonResource($parameter);
    }

    public function store(Template $template, TemplateParameterStoreRequest $request) {
        $parameter = $template->parameters()->create($request->validated());
        return new JsonResource($parameter);
    }

    public function update(TemplateParameterStoreRequest $request, Template $template, TemplateParameter $parameter) {
        $parameter->fill($request->validated())->save();
        return new JsonResource($parameter);
    }

    public function destroy(Template $template, TemplateParameter $parameter) {
        $parameter->delete();
        return response()->json();
    }
}
