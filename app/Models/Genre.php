<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    // for m-to-m condition
    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
