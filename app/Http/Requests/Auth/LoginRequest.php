<?php


namespace App\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array {
        return [
            'email' => 'string|required|max:128',
            'password' => 'string|required|max:128',
        ];
    }
}
