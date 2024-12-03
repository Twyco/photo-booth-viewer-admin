<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //TODO check Admin Rights
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30', //TODO Check formatting and change max length
            'description' => 'nullable|string|max:200',
            'path' => 'required|string|max:255',
            'cover_file_name' => 'nullable|string|max:255',
            'event_date' => 'date',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 30 characters.',
            'description.max' => 'The description may not be greater than 200 characters.',
            'path.required' => 'The path field is required.',
            'path.max' => 'The path may not be greater than 255 characters.',
            'cover_file_name.max' => 'The cover may not be greater than 255 characters.',
            'event_date.date' => 'The published date must be a valid date.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Wir werfen eine Exception, die eine JSON-Antwort zurückgibt
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

}
