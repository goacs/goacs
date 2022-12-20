<?php


namespace App\Http\Requests\Device;


use Illuminate\Foundation\Http\FormRequest;

class DeviceCreateRequest extends FormRequest
{
    public function rules(): array {
        return [
            'serial_number' => 'required|string|unique:device',
            'product_class' => 'string',
            'model_name' => 'string',
            'debug' => 'boolean',
        ];
    }
}
