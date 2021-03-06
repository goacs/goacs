<?php

declare(strict_types=1);


namespace App\Http\Requests\Settings\User;


use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function rules(): array {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required_with:password'
        ];
    }
}
