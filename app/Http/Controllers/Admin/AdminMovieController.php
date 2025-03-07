<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Genre;
use App\Models\Movie;
use Exception;
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

    public function adminStoreMovie(CreateMovieRequest $request)
    {
        // request with validated data
        $data = $request->validated();
        // prepare movie 
        $movie = new Movie();
        $movie->title = $data['title'];
        $movie->image_url = $data['image_url'];
        $movie->published_year = $data['published_year'];
        $movie->is_showing = $data['is_showing'];
        $movie->description = $data['description'];
        // start transaction
        DB::beginTransaction();
        try {
            // get or create genre if exists
            $genre = Genre::firstOrCreate(['name' => $data['genre']]);
            // add fk to movie model
            $movie->genre_id = $genre->id;
            // save to db
            $movie->save();
            DB::commit();
            return redirect()->route('admin.list.movie')->with('success', '保存しました');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('admin.list.movie')->withErrors($e->getMessage())->setStatusCode(500);
        }
    }

    public function adminGetMovieDetail($id)
    {
        $movie = Movie::with('genre', 'schedules')->findOrFail($id);
        return view('adminGetMovieDetail', compact('movie'));
    }

    public function adminEditMovie($id)
    {
        $movie = Movie::with('genre')->findOrFail($id);
        return view('adminEditMovie', compact('movie'));
    }

    public function adminUpdateMovie(UpdateMovieRequest $request, $id)
    {
        // request with validated data
        $data = $request->validated();
        // check exists movie
        $movie = Movie::findOrFail($id);
        // start transaction
        DB::beginTransaction();
        try {
            // get or create genre if exists
            $genre = Genre::firstOrCreate(['name' => $data['genre']]);
            // try to update movie
            $movie->update([
                'title' =>  $data['title'],
                'image_url' => $data['image_url'],
                'published_year' => $data['published_year'],
                'is_showing' => $data['is_showing'],
                'description' => $data['description'],
                // set new fk to movie model
                'genre_id' => $genre->id,
            ]);
            DB::commit();
            return redirect()->route('admin.edit.movie', $id)->with('success', '更新しました');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('admin.edit.movie')->withErrors($e->getMessage())->setStatusCode(500);
        }
    }

    public function adminDeleteMovie($id)
    {
        $movie = Movie::findOrFail($id);
        try {
            $movie->delete();
            return redirect()->route('admin.list.movie')->with('success', '消しました');
        } catch (Exception $e) {
            return back()->withErrors('削除に失敗しました: ' . $e->getMessage())->setStatusCode(500);
        }
    }
}
