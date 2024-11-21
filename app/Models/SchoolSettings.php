<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSettings extends Model
{
    protected $fillable = [
        'id',
        'school_name',
        'current_year',
        'term_start_date',
        'term_end_date',
        'next_term_start_date',
        'next_term_end_date',
        'term',
        'school_motto',
        'school_vision',
        'logo_url',
    ];
}
