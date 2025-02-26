<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;

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
}
