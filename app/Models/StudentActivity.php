<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentActivity extends Model
{
    protected $fillable = [
        'student_id',
        'responsibilties',
        'clubs',
        'sports',
        'house_comment',
        'teacher_comment',
        'principal_comment',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
