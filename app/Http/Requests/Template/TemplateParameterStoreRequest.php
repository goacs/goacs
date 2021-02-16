<?php


namespace App\Http\Requests\Template;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemplateParameterStoreRequest extends FormRequest
{
    public function rules(): array {
        return [
            'name' => [
                'string',
                'required',
                ],
            'value' => 'string',
            'type' => 'string|required',
            'flags' => 'array'
        ];
    }
}
