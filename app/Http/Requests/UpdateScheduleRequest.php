<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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
            'movie_id' => ['required'],
            'start_time_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:end_time_date'],
            'start_time_time' => ['required', 'date_format:H:i'],
            'end_time_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_time_date'],
            'end_time_time' => ['required', 'date_format:H:i'],
            'start_date_time' => ['required', 'date_format:Y-m-d H:i'],
            'end_date_time' => ['required', 'date_format:Y-m-d H:i'],
        ];
    }

    public function prepareForValidation()
    {
        // prepare start_date_time and end_date_time for request
        $startDateTime = $this->input('start_time_date') . ' ' . $this->input('start_time_time');
        $endDateTime = $this->input('end_time_date') . ' ' . $this->input('end_time_time');

        // merge to request
        $this->merge([
            'start_date_time' => $startDateTime,
            'end_date_time' => $endDateTime,
        ]);
    }

    public function withValidator($validator)
    {
        // after default rule validate
        // then validate by custom rule
        $validator->after(function ($validator) {
            $data = $this->all();
            $startTime = Carbon::parse($data['start_time_date'] . ' ' . $data['start_time_time']);
            $endTime = Carbon::parse($data['end_time_date'] . ' ' . $data['end_time_time']);

            // check for throw error
            // check start time & end time are same?
            if ($startTime->equalTo($endTime)) {
                $validator->errors()->add('start_time_time', '開始時刻と終了時刻を同じにすることはできません');
                $validator->errors()->add('end_time_time', '開始時刻と終了時刻を同じにすることはできません');
            }

            // check start time is greater than end time?
            if ($startTime->greaterThan($endTime)) {
                $validator->errors()->add('start_time_time', '開始時刻は終了時刻より遅くなってはいけません');
                $validator->errors()->add('end_time_time', '開始時刻は終了時刻より遅くなってはいけません');
            }

            // check difference between start time & and end time <= 5 min?
            if ($startTime->diffInMinutes($endTime) <= 5) {
                $validator->errors()->add('start_time_time', '所要時間は最低でも5分でなければなりません');
                $validator->errors()->add('end_time_time', '所要時間は最低でも5分でなければなりません');
            }
        });
    }


}
