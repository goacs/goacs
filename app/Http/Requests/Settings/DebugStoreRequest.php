<?php

declare(strict_types=1);


namespace App\Http\Requests\Settings;


use Illuminate\Foundation\Http\FormRequest;

class DebugStoreRequest extends FormRequest
{
    public function rules(): array {
        return [
            'debug' => 'required|boolean',
            'devices' => 'array',
            'devices.*' => 'integer|exists:device,id'
        ];
    }
}
