<?php

declare(strict_types=1);


namespace App\Http\Requests\Settings;


use Illuminate\Foundation\Http\FormRequest;

class SettingStoreRequest extends FormRequest
{
    public function rules(): array {
        return [
            'pii' => 'required|string|regex:/^(\d+)-(\d+)$/',
            'lookup_cache_ttl' => 'required|integer|gt:1',
            'connection_request_username' => 'required|string',
            'connection_request_password' => 'required|string',
            'mappings' => 'array',
            'conversation_log' => 'required|boolean',
            'read_behaviour' => 'required|in:new,boot,none'
        ];
    }
}
