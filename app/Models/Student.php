<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\Stream;
use App\Models\ClassForm;
use App\Models\StudentDetail;
use App\Models\StudentActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'name',
        'adm_no',
        'term',
        'form',
        'stream_id',
        'form_sequence_number',
    ];

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function classForm(): BelongsTo
    {
        //return formnya yg berkait student
        return $this->belongsTo(ClassForm::class, 'form', 'id');
    }

    public function exam(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function studentdetails(): HasOne
    {
        return $this->hasOne(StudentDetail::class);
    }

    public function activity(): HasOne
    {
        return $this->hasOne(StudentActivity::class);
    }
}
