<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Movie;
use App\Models\Schedule;
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
        return view('adminEditSchedule', compact('schedule'));
    }

    // waiting for refactor with UpdateScheduleRequest //
    public function adminUpdateSchedule(UpdateScheduleRequest $request, $scheduleId)
    {
        // request with validated data
        $data = $request->validated();
        // check exists schedule
        $schedule = Schedule::findOrFail($scheduleId);
        // try to update
        try {
            $schedule->update([
                'start_time' => $data['start_date_time'],
                'end_time' => $data['end_date_time'],
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
        return view('adminCreateSchedule', compact('movie'));
    }

    // use custom validate request 'CreateScheduleRequest' 
    public function adminStoreSchedule(CreateScheduleRequest $request)
    {
        // request with validated data
        $data = $request->validated();
        // schedule part
        $schedule = new Schedule();
        $schedule->start_time = $data['start_date_time'];
        $schedule->end_time = $data['end_date_time'];
        $schedule->movie_id = $data['movie_id'];
        // try to save
        try {
            $schedule->save();
            return redirect()->route('admin.list.schedule')->with('success', '保存しました');
        } catch (Exception $e) {
            return redirect()->route('admin.list.schedule')->withErrors($e->getMessage())->setStatusCode(500);
        }
    }
}
