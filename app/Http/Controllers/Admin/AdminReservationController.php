<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdminReservationRequest;
use App\Http\Requests\UpdateAdminReservationRequest;
use App\Models\Movie;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\Screen;
use App\Models\Sheet;
use Carbon\CarbonImmutable;
use Exception;

class AdminReservationController extends Controller
{

    public function adminGetReservations()
    {
        // show only screening not start yet (end time >= now)
        $reservations = Reservation::with(['schedule.movie', 'sheet'])
            ->whereHas('schedule', function ($query) {
                $query->where('start_time', '>=', CarbonImmutable::now());
            })
            ->select('id', 'schedule_id', 'sheet_id', 'name', 'email', 'is_canceled')
            ->get();
        return view('adminGetReservations', compact('reservations'));
    }

    public function adminCreateReservation()
    {
        // get list movie
        $movies = Movie::all();
        // get list schedule with not start yet
        $schedules = Schedule::where('start_time', '>=', CarbonImmutable::now())->get();
        // get all sheets
        $sheets = Sheet::all();
        // get current date
        $date = CarbonImmutable::now()->format('Y-m-d');
        return view('adminCreateReservation', compact('movies', 'schedules', 'sheets', 'date'));
    }

    public function adminStoreReservation(CreateAdminReservationRequest $request)
    {
        // request with validated data
        $data = $request->validated();
        // check exists schedule
        $schedule = Schedule::findOrFail($data['schedule_id']);
        // get date from selected schedule
        $date = $schedule->start_time->format('Y-m-d');
        // check for existing reservation in selected sheet_id & schedule_id & date
        $isReserved = Reservation::where('sheet_id',  $data['sheet_id'])
            ->where('schedule_id', $data['schedule_id'])
            ->where('date', $date)
            ->exists();
        // if exist means already reserved then redirect with error
        if ($isReserved) {
            return redirect()->back()->withErrors('この座席はすでに予約済みです。');
        }
        // new reservation model
        $reservation = new Reservation();
        $reservation->schedule_id = $data['schedule_id'];
        $reservation->sheet_id = $data['sheet_id'];
        $reservation->date = $date;
        $reservation->email = $data['email'];
        $reservation->name = $data['name'];
        // default hard set is_canceled to false
        $reservation->is_canceled = false;
        try {
            // create reservation
            $reservation->save();
            // redirect with success message
            return redirect()->route('admin.list.reservation')
                ->with('success', '予約が完了しました。');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->setStatusCode(500);
        }
    }

    public function adminEditReservation($id)
    {
        // get reservation by id
        $reservation = Reservation::with(['schedule.movie', 'sheet'])->find($id);
        // get list movie
        $movies = Movie::all();
        // get list schedule with not start yet
        $schedules = Schedule::where('start_time', '>=', CarbonImmutable::now())->get();
        // get all sheet 
        $sheets = Sheet::all();
        // get current date 
        $date = CarbonImmutable::now()->format('Y-m-d');
        return view('adminEditReservation', compact('reservation', 'movies', 'schedules', 'sheets', 'date'));
    }

    public function adminUpdateReservation(UpdateAdminReservationRequest $request, $id)
    {
        // request with validated data
        $data = $request->validated();
        // check exists schedule
        $schedule = Schedule::findOrFail($data['schedule_id']);
        // get date from selected schedule
        $date = $schedule->start_time->format('Y-m-d');
        // check for existing reservation in selected sheet_id & schedule_id & date
        $isReserved = Reservation::where('sheet_id',  $data['sheet_id'])
            ->where('schedule_id', $data['schedule_id'])
            ->where('date', $date)
            ->exists();
        // if exist means already reserved then redirect with error
        if ($isReserved) {
            return redirect()->back()->withErrors('この座席はすでに予約済みです。');
        }
        // get exist reservation by id
        $reservation = Reservation::findOrFail($id);
        try {
            // try to update reservation
            $reservation->update([
                'schedule_id' => $data['schedule_id'],
                'sheet_id' => $data['sheet_id'],
                'date' => $date,
                'email' => $data['email'],
                'name' => $data['name']
            ]);
            // redirect with success message
            return redirect()->route('admin.list.reservation')
                ->with('success', '予約が完了しました。');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->setStatusCode(500);
        }
    }

    public function adminDeleteReservation($id)
    {
        $reservation = Reservation::findOrFail($id);
        try {
            $reservation->delete();
            return redirect()->route('admin.list.reservation')->with('success', '消しました');
        } catch (Exception $e) {
            return back()->withErrors('削除に失敗しました: ' . $e->getMessage())->setStatusCode(500);
        }
    }
}
