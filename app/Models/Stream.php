<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stream extends Model
{
    protected $fillable = [
        'name',
        'class_id',
    ];

    /**
     * Summary of classForm
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classForm(): BelongsTo
    {
        return $this->belongsTo(ClassForm::class, 'class_id');
    }
}
