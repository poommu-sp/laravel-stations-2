<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Schedule;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminScheduleController extends Controller
{
    public function adminGetSchedules()
    {
        // get movie which only has schedule
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
            'movie_id' => 'required|integer',
            'start_time_date' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'start_time_time' => 'required|date_format:H:i',
            'end_time_date' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'end_time_time' => 'required|date_format:H:i',
        ]);

        $schedule = Schedule::findOrFail($scheduleId);

        // combine date & time
        $start_time =  $validated['start_time_date'] . ' ' .  $validated['start_time_time'];
        $end_time =  $validated['end_time_date'] . ' ' .  $validated['end_time_time'];

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
        $schedule = Movie::find($scheduleId);

        if (!$schedule) {
            abort(404, '消しませんでした');
        }

        $schedule->delete();

        return redirect()->route('admin.list.schedule')->with('success', '消しました');
    }

    public function adminCreateSchedule($id)
    {
        $movie = Movie::findOrFail($id);
        return view('adminCreateSchedule',compact('movie'));
    }

    public function adminStoreSchedule(Request $request)
    {
        // validate 
        $validated =  $request->validate([
            'movie_id' => 'required|integer',
            'start_time_date' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'start_time_time' => 'required|date_format:H:i',
            'end_time_date' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'end_time_time' => 'required|date_format:H:i',
        ]);

        // combine date & time
        $start_time =  $validated['start_time_date'] . ' ' .  $validated['start_time_time'];
        $end_time =  $validated['end_time_date'] . ' ' .  $validated['end_time_time'];


        // movie part
        $schedule = new Schedule();
        $schedule->start_time = $start_time;
        $schedule->end_time = $end_time;
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
