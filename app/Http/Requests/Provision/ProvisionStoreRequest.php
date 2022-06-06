<?php

declare(strict_types=1);


namespace App\Http\Requests\Provision;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProvisionStoreRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules(): array {
        return [
            'name' => 'required|string',
            'events' => 'required',
            'script' => 'required',
            'templates' => 'array',
            'templates.*' => 'exists:templates,id',
            'rule' => 'string',
        ];
    }
}
