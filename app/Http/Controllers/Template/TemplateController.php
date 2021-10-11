<?php


namespace App\Http\Controllers\Template;


use App\Http\Controllers\Controller;
use App\Http\Requests\Template\TemplateStoreRequest;
use App\Http\Resource\Template\TemplateResource;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\QueryBuilder;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $query = QueryBuilder::for(Template::class)
            ->withCount('parameters')
            ->allowedFilters([
                'name',
                'parameters_count',
                'updated_at'
            ]);
        return $query->paginate(25);
    }

    public function show(Template $template) {
        return new TemplateResource($template);
    }

    public function store(TemplateStoreRequest $request) {
        $template = Template::create($request->validated());
        return new JsonResource($template);
    }

    public function update(Template $template, TemplateStoreRequest $request) {
        $template->fill($request->validated())->save();
        return new JsonResource($template);
    }
}
