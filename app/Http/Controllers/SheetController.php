<?php

namespace App\Http\Controllers;

use App\Models\Sheet;

class SheetController extends Controller
{
    public function getSheets()
    {
        // get all sheets group by row
        $sheets = Sheet::all()->groupBy('row');
        return view('getSheets', compact('sheets'));
    }

}