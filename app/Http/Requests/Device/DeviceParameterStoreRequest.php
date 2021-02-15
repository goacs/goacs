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
                Rule::unique('device_parameters','name')->ignore($this->parameter)
            ],
            'value' => 'string',
            'type' => 'string|required',
            'flags' => 'array'
        ];
    }
}
