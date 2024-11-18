<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    protected $fillable = [
        'student_id',
        'primary_school',
        'kcpe_year',
        'kcpe_marks',
        'kcpe_position',
    ];
}
