<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{

    public function getMovie(Request $request)
    {
        $keyword = $request->input('keyword');
        $is_showing = $request->input('is_showing');

        $query = Movie::query();

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

        return view('getMovie', compact('movies', 'keyword', 'is_showing'));
    }

    public function getAdminMovie()
    {
        $movies = Movie::all();
        return view('getAdminMovie', compact('movies'));
    }

    public function adminMovieCreate()
    {
        return view('getAdminMovieCreate');
    }

    public function adminMovieSave(Request $request)
    {
        // validate 
        $validated = $request->validate([
            'title' => 'required|unique:movies,title',
            'image_url' => 'required|url',
            'published_year' => 'required|integer',
            'description' => 'required|string',
            'is_showing' => 'required|nullable|boolean',
            'genre' => 'required|string',
        ]);
        // movie part
        $movie = new Movie();
        $movie->title = $validated['title'];
        $movie->image_url = $validated['image_url'];
        $movie->published_year = $validated['published_year'];
        $movie->is_showing = $request->has('is_showing') ? 1 : 0;;
        $movie->description = $validated['description'];

        // start transtion
        DB::beginTransaction();
        try {
            // create or update genre if exists
            $genre = Genre::firstOrCreate(['name' => $validated['genre']]);
            // add fk to model
            $movie->genre_id = $genre->id;
            // save
            $movie->save();
            DB::commit();
            return redirect()->route('admin.list')->with('success', '保存しました');
        } catch (Exception $exception) {
            DB::rollback();
            return redirect()->route('admin.list')->withErrors($exception)->setStatusCode(500);
        }
    }

    public function adminEditMovie($id)
    {
        $movie = Movie::find($id);
        return view('getAdminMovieEdit', compact('movie'));
    }

    public function adminMovieUpdate(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|unique:movies,title,' . $movie->id,
            'image_url' => 'required|url',
            'published_year' => 'required|integer',
            'description' => 'required|string',
            'is_showing' => 'required|nullable|boolean',
            'genre' => 'required|string',
        ]);
        // set is showing
        $is_showing = $request->has('is_showing') ? 1 : 0;
        try {
            // genre part
            // create or update genre if exists
            $genre = Genre::firstOrCreate(['name' => $validated['genre']]);
            Log::info($genre);
            Log::info($request);

            // update movie
            $movie->update([
                'title' =>  $validated['title'],
                'image_url' => $validated['image_url'],
                'published_year' => $validated['published_year'],
                'is_showing' => $is_showing,
                'description' => $validated['description'],
                // add fk to model
                'genre_id' => $genre->id,
            ]);

            DB::commit();
            return redirect()->route('edit', $movie->id)->with('success', '更新しました');
        } catch (Exception $exception) {
            DB::rollback();
            return redirect()->route('edit')->withErrors($exception)->setStatusCode(500);
        }
    }

    public function adminMovieDelete($id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            abort(404, '消しませんでした');
        }

        $movie->delete();

        return redirect()->route('admin.list')->with('success', '消しました');
    }
}
