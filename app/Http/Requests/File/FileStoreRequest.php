<?php


namespace App\Http\Requests\File;


use App\Models\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class FileStoreRequest extends FormRequest
{
    public function rules(): array {
        return [
            'file' => 'required|file'
        ];
    }

    public function withValidator(Validator $validator) {
        $validator->after(function(Validator $validator) {
            $filename = $this->file('file')->getClientOriginalName();
            $exist = File::where('filepath', $filename)->exists();
            if($exist) {
                $validator->errors()->add('file', 'File exist');
            }
        });
    }
}
