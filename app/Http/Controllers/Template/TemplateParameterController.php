<?php


namespace App\Http\Controllers\Template;


use App\Http\Controllers\Controller;
use App\Http\Requests\Template\TemplateParameterStoreRequest;
use App\Models\Filters\FlagFilter;
use App\Models\Template;
use App\Models\TemplateParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TemplateParameterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request, Template $template) {
        $query = QueryBuilder::for($template->parameters());
        $query->allowedFilters([
            'name',
            'type',
            'value',
            AllowedFilter::custom('flags', new FlagFilter())
        ]);
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
