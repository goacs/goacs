<?php


namespace App\Http\Controllers\Template;


use App\Http\Controllers\Controller;
use App\Http\Requests\Template\TemplateStoreRequest;
use App\Models\Template;
use App\Models\TemplateParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateParameterController extends Controller
{
    public function index(Request $request, Template $template) {
        $query = $template->parameters();
        $this->prepareFilter($request, $query);
        return $query->paginate(25);
    }

    public function show(TemplateParameter $parameter) {
        return new JsonResource($parameter);
    }

    public function store(TemplateStoreRequest $request) {
        $parameter = TemplateParameter::create($request->validated());
        return new JsonResource($parameter);
    }
}
