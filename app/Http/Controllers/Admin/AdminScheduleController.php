<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Screen;
use Carbon\Carbon;
use Exception;

class AdminScheduleController extends Controller
{
    public function adminGetSchedules()
    {
        // get movies which only has schedule
        $movies = Movie::whereHas('schedules')->get();
        return view('adminGetSchedules', compact('movies'));
    }

    public function adminGetScheduleDetail($scheduleId)
    {
        $schedule = Schedule::with('movie')->findOrFail($scheduleId);
        return view('adminGetScheduleDetail', compact('schedule'));
    }

    public function adminEditSchedule($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $screens = Screen::all();
        return view('adminEditSchedule', compact('schedule', 'screens'));
    }

    public function adminUpdateSchedule(UpdateScheduleRequest $request, $scheduleId)
    {
        // request with validated data
        $data = $request->validated();
        // parse start_time & end_time to carbon for next step calculate
        $startTime = Carbon::parse($data['start_time_date'] . ' ' . $data['start_time_time']);
        $endTime = Carbon::parse($data['end_time_date'] . ' ' . $data['end_time_time']);
        // check for throw error
        // check start time & end time are same?
        if ($startTime->equalTo($endTime)) {
            return back()->withErrors([
                'start_time_time' => '開始時刻と終了時刻を同じにすることはできません',
                'end_time_time' => '開始時刻と終了時刻を同じにすることはできません'
            ]);
        }
        // check start time is greater than end time?
        if ($startTime->greaterThan($endTime)) {
            return back()->withErrors([
                'start_time_time' => '開始時刻は終了時刻より遅くなってはいけません',
                'end_time_time' => '終了時刻は開始時刻より早くなってはいけません'
            ]);
        }
        // check difference between start time & and end time <= 5 min?
        if ($startTime->diffInMinutes($endTime) <= 5) {
            return back()->withErrors([
                'start_time_time' => '所要時間は最低でも5分でなければなりません',
                'end_time_time' => '所要時間は最低でも5分でなければなりません'
            ]);
        }
        // check exists schedule
        $schedule = Schedule::findOrFail($scheduleId);
        // try to update
        try {
            $schedule->update([
                'start_time' => $startTime,
                'end_time' => $endTime,
                'screen_id' => $data['screen_id'],
            ]);
            return redirect()->route('admin.edit.schedule', $scheduleId)->with('success', '更新しました');
        } catch (Exception $e) {
            return redirect()->route('admin.edit.schedule')->withErrors($e->getMessage())->setStatusCode(500);
        }
    }

    public function adminDeleteSchedule($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        try {
            $schedule->delete();
            return redirect()->route('admin.list.schedule')->with('success', '消しました');
        } catch (Exception $e) {
            return back()->withErrors('削除に失敗しました: ' . $e->getMessage())->setStatusCode(500);
        }
    }

    public function adminCreateSchedule($id)
    {
        $movie = Movie::findOrFail($id);
        $screens = Screen::all();
        return view('adminCreateSchedule', compact('movie', 'screens'));
    }

    public function adminStoreSchedule(CreateScheduleRequest $request)
    {
        // request with validated data
        $data = $request->validated();
        // parse start_time & end_time to carbon for next step calculate
        $startTime = Carbon::parse($data['start_time_date'] . ' ' . $data['start_time_time']);
        $endTime = Carbon::parse($data['end_time_date'] . ' ' . $data['end_time_time']);
        // check for throw error
        // check start time & end time are same?
        if ($startTime->equalTo($endTime)) {
            return back()->withErrors([
                'start_time_time' => '開始時刻と終了時刻を同じにすることはできません',
                'end_time_time' => '開始時刻と終了時刻を同じにすることはできません'
            ]);
        }
        // check start time is greater than end time?
        if ($startTime->greaterThan($endTime)) {
            return back()->withErrors([
                'start_time_time' => '開始時刻は終了時刻より遅くなってはいけません',
                'end_time_time' => '終了時刻は開始時刻より早くなってはいけません'
            ]);
        }
        // check difference between start time & and end time <= 5 min?
        if ($startTime->diffInMinutes($endTime) <= 5) {
            return back()->withErrors([
                'start_time_time' => '所要時間は最低でも5分でなければなりません',
                'end_time_time' => '所要時間は最低でも5分でなければなりません'
            ]);
        }
        // schedule part
        $schedule = new Schedule();
        $schedule->start_time = $startTime;
        $schedule->end_time = $endTime;
        $schedule->movie_id = $data['movie_id'];
        $schedule->screen_id = $data['screen_id'];
        // try to save
        try {
            $schedule->save();
            $movieId = $data['movie_id'];
            return redirect()->route('admin.movies.show', $movieId)->with('success', '保存しました');
        } catch (Exception $e) {
            return redirect()->route('admin.list.schedule')->withErrors($e->getMessage())->setStatusCode(500);
        }
    }
}
