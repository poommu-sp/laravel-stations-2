<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = ['title', 'image_url', 'published_year', 'description', 'is_showing', 'genre_id'];

    use HasFactory;

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function schedules()
{
    return $this->hasMany(Schedule::class);
}

}
