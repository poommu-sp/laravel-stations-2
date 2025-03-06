<?php

use App\Http\Controllers\Admin\AdminMovieController;
use App\Http\Controllers\Admin\AdminScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SheetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// practices
Route::get('/practice', [PracticeController::class, 'sample']);
Route::get('/practice2', [PracticeController::class, 'sample2']);
Route::get('/practice3', [PracticeController::class, 'sample3']);
Route::get('/getPractice', [PracticeController::class, 'getPractice']);

// movies
Route::get('/movies', [MovieController::class, 'getMovies'])->name('movie.search');
Route::get('/movies/{id}',[MovieController::class, 'getMovieDetail'])->name('movie.detail');
// reservation
Route::get('/movies/{movie_id}/schedules/{schedule_id}/sheets', [MovieController::class, 'sheetReservation'])->name('sheet.reservation');
Route::get('/movies/{movie_id}/schedules/{schedule_id}/reservations/create', [MovieController::class, 'createReservation'])->name('create.reservation');
Route::post('/reservations/store', [MovieController::class, 'storeReservation'])->name('store.reservation');
// sheets
Route::get('/sheets',[SheetController::class, 'getSheets']); 
// admin movies
Route::get('/admin/movies',[AdminMovieController::class, 'adminGetMovies'])->name('admin.list.movie');
Route::get('/admin/movies/create', [AdminMovieController::class, 'adminCreateMovie']);
Route::post('/admin/movies/store', [AdminMovieController::class, 'adminStoreMovie'])->name('admin.store.movie');
Route::get('/admin/movies/{id}', [AdminMovieController::class, 'adminGetMovieDetail'])->name('admin.get.movie');
Route::get('/admin/movies/{id}/edit', [AdminMovieController::class, 'adminEditMovie'])->name('admin.edit.movie');
Route::patch('/admin/movies/{id}/update', [AdminMovieController::class, 'adminUpdateMovie'])->name('admin.update.movie');
Route::delete('/admin/movies/{id}/destroy', [AdminMovieController::class, 'adminDeleteMovie'])->name('admin.delete.movie');
// admin schedules
Route::get('/admin/schedules', [AdminScheduleController::class, 'adminGetSchedules'])->name('admin.list.schedule');
Route::get('/admin/schedules/{scheduleId}', [AdminScheduleController::class, 'adminGetScheduleDetail'])->name('admin.get.schedule');
Route::get('/admin/schedules/{scheduleId}/edit', [AdminScheduleController::class, 'adminEditSchedule'])->name('admin.edit.schedule');
Route::patch('/admin/schedules/{scheduleId}/update', [AdminScheduleController::class, 'adminUpdateSchedule'])->name('admin.update.schedule');
Route::delete('/admin/schedules/{scheduleId}/destroy', [AdminScheduleController::class, 'adminDeleteSchedule'])->name('admin.delete.schedule');
// admin create schedule for selected movie id
Route::get('/admin/movies/{id}/schedules/create', [AdminScheduleController::class, 'adminCreateSchedule'])->name('admin.create.schedule');
Route::post('/admin/movies/{id}/schedules/store', [AdminScheduleController::class, 'adminStoreSchedule'])->name('admin.store.schedule');
