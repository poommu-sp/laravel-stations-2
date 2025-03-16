<?php

use App\Http\Controllers\Admin\AdminMovieController;
use App\Http\Controllers\Admin\AdminReservationController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SheetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// no auth
// practices 
Route::get('/practice', [PracticeController::class, 'sample']);
Route::get('/practice2', [PracticeController::class, 'sample2']);
Route::get('/practice3', [PracticeController::class, 'sample3']);
Route::get('/getPractice', [PracticeController::class, 'getPractice']);

// auth user
Route::middleware(['auth'])->group(function () {
    // movies
    Route::get('/movies', [MovieController::class, 'getMovies'])->name('movie.search');
    Route::get('/movies/{id}', [MovieController::class, 'getMovieDetail'])->name('movie.detail');
    // reservation
    Route::get('/movies/{movie_id}/schedules/{schedule_id}/sheets', [MovieController::class, 'sheetReservation'])->name('sheet.reservation');
    Route::get('/movies/{movie_id}/schedules/{schedule_id}/reservations/create', [MovieController::class, 'createReservation'])->name('create.reservation');
    Route::post('/reservations/store', [MovieController::class, 'storeReservation'])->name('store.reservation');
    // sheets
    Route::get('/sheets', [SheetController::class, 'getSheets']);
});

// auth admin
// admin movies
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/movies', [AdminMovieController::class, 'adminGetMovies'])->name('admin.list.movie');
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
    // admin reservation
    Route::get('/admin/reservations/', [AdminReservationController::class, 'adminGetReservations'])->name('admin.list.reservation');
    Route::get('/admin/reservations/create', [AdminReservationController::class, 'adminCreateReservation']);
    Route::post('/admin/reservations', [AdminReservationController::class, 'adminStoreReservation'])->name('admin.store.reservation');
    Route::get('/admin/reservations/{id}', [AdminReservationController::class, 'adminGetReservationDetail'])->name('admin.get.reservation');
    Route::patch('/admin/reservations/{id}', [AdminReservationController::class, 'adminUpdateReservation'])->name('admin.update.reservation');
    Route::delete('/admin/reservations/{id}', [AdminReservationController::class, 'adminDeleteReservation'])->name('admin.delete.reservation');
});

require __DIR__ . '/auth.php';
