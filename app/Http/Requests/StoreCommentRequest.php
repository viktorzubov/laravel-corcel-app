<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:200'],
            'content' => ['required', 'string', 'max:5000'],
        ];
    }
}
