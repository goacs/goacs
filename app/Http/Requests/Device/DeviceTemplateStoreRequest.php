<?php


namespace App\Http\Requests\Device;


use Illuminate\Foundation\Http\FormRequest;

class DeviceTemplateStoreRequest extends FormRequest
{
    public function rules(): array {
        return [
            'template_id' => 'integer|required',
            'priority' => 'integer|required'
        ];
    }
}
