<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    protected $fillable = ['movie_id','start_time','end_time'];

    // cast to Carbon instance
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    use HasFactory;

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
    
}
