<?php

declare(strict_types=1);


namespace App\Http\Requests\Device;


use Illuminate\Foundation\Http\FormRequest;

class DownloadDeviceLogRequest extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'session_id' => 'required|exists:logs,session_id'
        ];
    }
}
