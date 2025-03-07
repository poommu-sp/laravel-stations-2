<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateReservationRequest extends FormRequest
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
            'schedule_id' => ['required', Rule::exists('schedules', 'id')],
            'sheet_id' => ['required', Rule::exists('sheets', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:strict,dns', 'max:255'],
            'date' => ['required', 'date_format:Y-m-d']
        ];
    }

    public function prepareForValidation()
    {
        // parse received date to format Y-m-d before validate
        if ($this->has('date')) {
            $this->merge([
                'date' => Carbon::parse($this->input('date'))->format('Y-m-d'),
            ]);
        }
    }
}
