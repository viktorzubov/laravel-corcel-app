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
        $guestRequired = $this->user() ? 'nullable' : 'required';

        return [
            'author' => [$guestRequired, 'string', 'max:100'],
            'email' => [$guestRequired, 'email', 'max:200'],
            'content' => ['required', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
