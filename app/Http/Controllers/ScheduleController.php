<?php

namespace App\Http\Controllers;

use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function getSchedules()
    {
        $schedules = Schedule::all();

        return view('getSchedules', compact('schedules'));
    }

}