<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminReservationRequest extends FormRequest
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
            'movie_id' => ['required', Rule::exists('movies', 'id')],
            'schedule_id' => ['required', Rule::exists('schedules', 'id')],
            'sheet_id' => ['required',  Rule::exists('sheets', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:strict,dns', 'max:255'],
        ];
    }
}
