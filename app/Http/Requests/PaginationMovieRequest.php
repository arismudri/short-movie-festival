<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PaginationMovieRequest extends FormRequest
{
    public function rules()
    {
        return [
            'page' => 'nullable|numeric',
            'per_page' => 'required|numeric',
            'search' => 'nullable|string'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()

        ]));
    }
}
