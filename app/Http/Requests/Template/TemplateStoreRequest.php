<?php


namespace App\Http\Requests\Template;


use Illuminate\Foundation\Http\FormRequest;

class TemplateStoreRequest extends FormRequest
{
    public function rules(): array {
        return [
            'name' => 'required|string|unique:templates',
        ];
    }
}
