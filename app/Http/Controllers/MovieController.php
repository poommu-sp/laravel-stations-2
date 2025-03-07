<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReservationRequest;
use App\Models\Movie;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\Sheet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{

    public function getMovies(Request $request)
    {
        // retrieve request param
        $keyword = $request->input('keyword');
        $is_showing = $request->input('is_showing');
        // start query builder
        $query = Movie::query();
        // Eager load 'genre' relationship
        $query->with('genre');
        // check is_showing is not null to add query filter is_showing
        if ($is_showing !== null) {
            $query->where('is_showing', $is_showing === "1" ? true : false);
        }
        // check not empty keyword to add query filter title & description
        if (!empty($keyword)) {
            $query->where('title', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        }
        // query with pagination 20 per page
        $movies = $query->paginate(20);
        return view('getMovies', compact('movies', 'keyword', 'is_showing'));
    }

    public function getMovieDetail($id)
    {
        $movie = Movie::with(['schedules', 'genre'])->findOrFail($id);
        return view('getMovieDetail', compact('movie'));
    }

    public function sheetReservation($movie_id, $schedule_id, Request $request)
    {
        // check query param date is not exits
        if (!$request->has('date')) {
            abort(400, 'dateパラメータは必須です。');
        }
        // get query param date
        $date = $request->query('date');
        // get all seats group by row
        $sheets = Sheet::all()->groupBy('row');
        // get all reserved seats in selected schedule_id & date
        $reservedSeats = Reservation::where('schedule_id', $schedule_id)
            ->where('date', $date)
            // get field sheet_id only and parse them to array
            ->pluck('sheet_id')->toArray();
        return view('sheetReservation', compact('movie_id', 'schedule_id', 'date', 'sheets', 'reservedSeats'));
    }

    public function createReservation($movie_id, $schedule_id, Request $request)
    {
        // check query param date and sheetId is not exits
        if (!$request->has('date') || !$request->has('sheetId')) {
            abort(400, 'dateとsheet_idパラメータは必須です。');
        }
        // get $sheet_id and $date from query param
        $sheet_id = $request->query('sheetId');
        $date = $request->query('date');
        // parse date to Carbon Y-m-d format 
        $date = Carbon::parse($date)->format('Y-m-d');
        // check for existing reservation in selected sheet_id & schedule_id & date
        $isReserved = Reservation::where('sheet_id',  $sheet_id)
            ->where('schedule_id', $schedule_id)
            ->where('date', $date)
            ->exists();
        // if exist means already reserved then abort 400 with error
        if ($isReserved) {
            abort(400, 'この座席はすでに予約済みです。');
        }
        return view('createReservation', compact('movie_id', 'schedule_id', 'date', 'sheet_id'));
    }

    public function storeReservation(CreateReservationRequest $request)
    {
        // request with validated data
        $data = $request->validated();
        // check for existing reservation in selected sheet_id & schedule_id & date
        $isReserved = Reservation::where('sheet_id',  $data['sheed_id'])
            ->where('schedule_id', $data['schedule_id'])
            ->where('date', $data['date'])
            ->exists();
        // if exist means already reserved then redirect with error
        if ($isReserved) {
            return redirect()->back()->withErrors('この座席はすでに予約済みです。');
        }
        // new reservation model
        $reservation = new Reservation();
        $reservation->schedule_id = $data['schedule_id'];
        $reservation->sheet_id = $data['sheet_id'];
        $reservation->date = $data['date'];
        $reservation->email = $data['email'];
        $reservation->name = $data['name'];
        // default hard set is_canceled to false
        $reservation->is_canceled = false;
        try {
            // create reservation
            $reservation->save();
            // redirect with success message
            return redirect()->route('movie.search')
                ->with('success', '予約が完了しました。');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->setStatusCode(500);
        }
    }
}
