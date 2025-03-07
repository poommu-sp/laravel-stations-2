<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Exception;


class AdminReservationController extends Controller 
{

    public function adminGetReservations()
    {

    }

    public function adminCreateReservation()
    {
        
    }

    public function adminStoreReservation()
    {
        
    }

    public function adminGetReservationDetail()
    {

    }

    public function adminUpdateReservation()
    {

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