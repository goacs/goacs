<?php


namespace App\Http\Requests\Device;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeviceParameterStoreRequest extends FormRequest
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
