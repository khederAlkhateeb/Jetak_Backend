<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['student_id', 'package_type', 'start_date', 'end_date', 'status', 'daily_quantity'])]
class BreadSubscription extends Model
{
     use HasFactory;

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
