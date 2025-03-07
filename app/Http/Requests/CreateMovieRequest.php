<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'unique:movies,title'],
            'image_url' => ['required', 'url'],
            'published_year' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'description' => ['required', 'string'],
            'is_showing' => ['nullable', 'boolean'],
            'genre' => ['required', 'string'],
        ];
    }
}
