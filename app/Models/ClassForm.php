<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassForm extends Model
{
    protected $table = 'class_forms';

    protected $fillable = [
        'name',
    ];
}
