<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    protected $fillable = ['movie_id','start_time','end_time', 'screen_id'];

    // cast to Carbon instance
    protected $casts = [
        'start_time' => 'immutable_datetime',
        'end_time' => 'immutable_datetime',
    ];

    use HasFactory;

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    // has many reservation
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    
}
