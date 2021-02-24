<?php


namespace App\Http\Requests\Settings\Task;


use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function rules(): array {
        return [
            'name' => 'required|string',
            'payload' => 'array',
            'infinite' => 'bool',
            'on_request' => 'string',
            'not_before' => 'date'
        ];
    }
}
