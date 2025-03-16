<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'schedule_id', 'sheet_id', 'date', 'name', 'email', 'is_canceled'];

    // has one schedule
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    // has one sheet
    public function sheet()
    {
        return $this->belongsTo(Sheet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
