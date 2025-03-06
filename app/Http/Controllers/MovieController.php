<?php

namespace App\Http\Controllers;

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
        $keyword = $request->input('keyword');
        $is_showing = $request->input('is_showing');

        $query = Movie::query();
        // Eager load 'genre' relationship
        $query->with('genre');

        // check not null to add query filter for showing
        if ($is_showing !== null) {
            $query->where('is_showing', $is_showing === "1" ? true : false);
        }
        // check not null to add query filter title
        if (!empty($keyword)) {
            $query->where('title', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        }

        // if not match all condition that means show all (query with no filter)
        // query with pagination
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
        // check query param date is exits
        if (!$request->has('date')) {
            abort(400, 'dateパラメータは必須です。');
        }
        $date = $request->query('date');

        // get all seats
        $sheets = Sheet::all()->groupBy('row');

        // get reserved seats
        $reservedSeats = Reservation::where('schedule_id', $schedule_id)
            ->where('date', $date)
            // get only field sheet_id and parse to array
            ->pluck('sheet_id')->toArray();

        return view('sheetReservation', compact('movie_id', 'schedule_id', 'date', 'sheets', 'reservedSeats'));
    }

    public function createReservation($movie_id, $schedule_id, Request $request)
    {
        // check query param date and sheetId is exits
        if (!$request->has('date') || !$request->has('sheetId')) {
            abort(400, 'dateとsheet_idパラメータは必須です。');
        }
        $sheet_id = $request->query('sheetId');
        $date = $request->query('date');

        // parse date to Y-m-d format
        $date = Carbon::parse($date)->format('Y-m-d');

        // check for existing reservation
        $isReserved = Reservation::where('sheet_id',  $sheet_id)
            ->where('schedule_id', $schedule_id)
            ->where('date', $date)
            ->exists();
        // if exist means already reserved then redirect with error
        if ($isReserved) {
            abort(400, 'この座席はすでに予約済みです。');
        }

        return view('createReservation', compact('movie_id', 'schedule_id', 'date', 'sheet_id'));
    }

    public function storeReservation(Request $request)
    {
        // validate
        // means schedule_id exists in schedules table column id
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'sheet_id' => 'required|exists:sheets,id',
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // parse date to Y-m-d format
        $date = Carbon::parse($validated['date'])->format('Y-m-d');

        // check for existing reservation
        $isReserved = Reservation::where('sheet_id',  $validated['sheet_id'])
            ->where('schedule_id', $validated['schedule_id'])
            ->where('date', $date)
            ->exists();
        // if exist means already reserved then redirect with error
        if ($isReserved) {
            return redirect()->back()->withErrors('この座席はすでに予約済みです。');
        }

        // new reservation
        $reservation = new Reservation();
        $reservation->schedule_id = $validated['schedule_id'];
        $reservation->sheet_id = $validated['sheet_id'];
        $reservation->date = $date;
        $reservation->email = $validated['email'];
        $reservation->name = $validated['name'];
        // default set is_canceled to false
        $reservation->is_canceled = false;

        // start transaction
        DB::beginTransaction();
        try {
            // create reservation
            $reservation->save();
            DB::commit();

            // get movie_id from request
            // use for redirect when success but can't use in test then skip
            // $movie_id = $request->input('movie_id');

            // redirect with success message
            return redirect()->route('movie.search')
                ->with('success', '予約が完了しました。');
        } catch (Exception $exception) {
            DB::rollback();
            return redirect()->back()->withErrors($exception->getMessage())->setStatusCode(500);
        }
    }
}
