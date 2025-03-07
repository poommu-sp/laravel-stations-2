<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{

    protected $fillable = ['column', 'row'];

    use HasFactory;

    // has many reservation
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
