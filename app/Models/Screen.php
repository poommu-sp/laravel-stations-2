<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
    protected $fillable = [
        'name',
    ];

    use HasFactory;

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

}
