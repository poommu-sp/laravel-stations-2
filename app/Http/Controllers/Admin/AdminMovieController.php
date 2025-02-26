<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMovieController extends Controller
{
    public function adminGetMovies()
    {
        $movies = Movie::with('genre')->get(); 
        return view('adminGetMovies', compact('movies'));
    }

    public function adminCreateMovie()
    {
        return view('adminCreateMovie');
    }

    public function adminStoreMovie(Request $request)
    {
        // validate 
        $validated = $request->validate([
            'title' => 'required|unique:movies,title',
            'image_url' => 'required|url',
            'published_year' => 'required|integer|min:1900|max:' . date('Y'),
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
            return redirect()->route('admin.list.movie')->with('success', '保存しました');
        } catch (Exception $exception) {
            DB::rollback();
            return redirect()->route('admin.list.movie')->withErrors($exception)->setStatusCode(500);
        }
    }

    public function adminGetMovieDetail($id)
    {
        $movie = Movie::with('genre')->find($id);
        return view('adminGetMovieDetail', compact('movie'));
    } 

    public function adminEditMovie($id)
    {
        $movie = Movie::with('genre')->find($id);
        return view('adminEditMovie', compact('movie'));
    }

    public function adminUpdateMovie(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|unique:movies,title,' . $movie->id,
            'image_url' => 'required|url',
            'published_year' => 'required|integer|min:1900|max:' . date('Y'),
            'description' => 'required|string',
            'is_showing' => 'required|nullable|boolean',
            'genre' => 'required|string',
        ]);
        // set is showing
        $is_showing = $request->has('is_showing') ? 1 : 0;
        // start transtion
        DB::beginTransaction();
        try {
            // genre part
            // create or update genre if exists
            $genre = Genre::firstOrCreate(['name' => $validated['genre']]);
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
            return redirect()->route('admin.edit.movie', $movie->id)->with('success', '更新しました');
        } catch (Exception $exception) {
            DB::rollback();
            return redirect()->route('admin.edit.movie')->withErrors($exception)->setStatusCode(500);
        }
    }

    public function adminDeleteMovie($id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            abort(404, '消しませんでした');
        }

        $movie->delete();

        return redirect()->route('admin.list.movie')->with('success', '消しました');
    }

    
}