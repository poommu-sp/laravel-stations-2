<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateScheduleRequest extends FormRequest
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
            'screen_id' => ['required', Rule::exists('screens', 'id')],
        ];
    }

    public function withValidator($validator)
    {
        // check overlap schedule
        $validator->after(function ($validator) {
            $data = $this->all();
            $startTime = Carbon::parse($data['start_time_date'] . ' ' . $data['start_time_time']);
            $endTime = Carbon::parse($data['end_time_date'] . ' ' . $data['end_time_time']);
            $overlappingSchedule = Schedule::where('screen_id', $data['screen_id'])
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<', $startTime)
                                ->where('end_time', '>', $endTime);
                        });
                })
                ->exists();
            if ($overlappingSchedule) {
                $validator->errors()->add('screen_id', '指定されたスクリーンはすでにその時間帯に予約されています');
            }
        });
    }
}
