<?php


namespace App\Http\Requests\Device;


use Illuminate\Foundation\Http\FormRequest;

class DeviceAddObjectRequest extends FormRequest
{
    public function rules(): array {
        return [
            'name' => 'required|string'
        ];
    }
}
