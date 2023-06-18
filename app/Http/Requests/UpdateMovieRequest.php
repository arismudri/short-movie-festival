<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateMovieRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|numeric|exists:movies,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'artists' => 'nullable|array|max:10',
            'artists.*' => 'required|string|distinct',
            'genre' => 'required|string|in:comedy,horror,romance',
            'file' => 'required|mimetypes:video/mp4|max:50000',
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
