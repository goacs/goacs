<?php


namespace App\Http\Requests\Device;


use Illuminate\Foundation\Http\FormRequest;

class PatchParametersRequest extends FormRequest
{
    public function rules(): array {
        return [
            'parameters.*.name' => 'required|string',
            'parameters.*.value' => 'present|string',
            'parameters.*.type' => 'sometimes|string',
            'parameters.*.flags' => 'sometimes|string',
        ];
    }
}
