<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Schedule;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('adminEditSchedule', compact('schedule'));
    }

    public function adminUpdateSchedule(Request $request, $scheduleId)
    {
        $validated =  $request->validate([
            'movie_id' => 'required',
            'start_time_date' => 'required|date_format:Y-m-d',
            'start_time_time' => 'required|date_format:H:i',
            'end_time_date' => 'required|date_format:Y-m-d',
            'end_time_time' => 'required|date_format:H:i',
        ]);

        $schedule = Schedule::findOrFail($scheduleId);

        // combine date & time
        $start_time = $validated['start_time_date'] . ' ' . $validated['start_time_time'];
        $end_time = $validated['end_time_date'] . ' ' .  $validated['end_time_time'];

        // start transtion
        DB::beginTransaction();
        try {
            $schedule->update([
                'start_time' => $start_time,
                'end_time' => $end_time,
            ]);
            DB::commit();
            return redirect()->route('admin.edit.schedule', $scheduleId)->with('success', '更新しました');
        } catch (Exception $exception) {
            DB::rollback();
            return redirect()->route('admin.edit.schedule')->withErrors($exception)->setStatusCode(500);
        }
    }

    public function adminDeleteSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);

        if (!$schedule) {
            abort(404, '消しませんでした');
        }

        $schedule->delete();

        return redirect()->route('admin.list.schedule')->with('success', '消しました');
    }

    public function adminCreateSchedule($id)
    {
        $movie = Movie::findOrFail($id);
        return view('adminCreateSchedule', compact('movie'));
    }

    public function adminStoreSchedule(Request $request)
    {
        // validate 
        $validated =  $request->validate([
            'movie_id' => 'required',
            'start_time_date' => 'required|date_format:Y-m-d|before_or_equal:end_time_date',
            'start_time_time' => 'required|date_format:H:i',
            'end_time_date' => 'required|date_format:Y-m-d|after_or_equal:start_time_date',
            'end_time_time' => 'required|date_format:H:i',
        ]);

        // parse start_time & end_time to carbon for next step calculate
        $startTime = Carbon::parse($validated['start_time_time']);
        $endTime = Carbon::parse($validated['end_time_time']);

        // check condition for start_time & end_time
        if ($startTime->equalTo($endTime)) {
            return back()->withErrors([
                'start_time_time' => '開始時刻と終了時刻を同じにすることはできません',
                'end_time_time' => '開始時刻と終了時刻を同じにすることはできません'
            ]);
        }

        if ($startTime->greaterThan($endTime)) {
            return back()->withErrors([
                'start_time_time' => '開始時刻は終了時刻より遅くなってはいけません',
                'end_time_time' => '終了時刻は開始時刻より早くなってはいけません'
            ]);
        }

        if ($startTime->diffInMinutes($endTime) <= 5) {
            return back()->withErrors([
                'start_time_time' => '所要時間は最低でも5分でなければなりません',
                'end_time_time' => '所要時間は最低でも5分でなければなりません'
            ]);
        }
        
        // combine date & time
        $start_date_time = $validated['start_time_date'] . ' ' . $validated['start_time_time'];
        $end_date_time = $validated['end_time_date'] . ' ' .  $validated['end_time_time'];

        // movie part
        $schedule = new Schedule();
        $schedule->start_time = $start_date_time;
        $schedule->end_time = $end_date_time;
        $schedule->movie_id = $validated['movie_id'];

        // start transtion
        DB::beginTransaction();
        try {
            $schedule->save();
            DB::commit();
            return redirect()->route('admin.list.schedule')->with('success', '保存しました');
        } catch (Exception $exception) {
            DB::rollback();
            return redirect()->route('admin.list.schedule')->withErrors($exception)->setStatusCode(500);
        }
    }
}
