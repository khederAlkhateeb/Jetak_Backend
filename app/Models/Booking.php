<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['student_id', 'hall_id', 'booking_date', 'start_time', 'end_time', 'total_price', 'status'])]

class Booking extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }
}
