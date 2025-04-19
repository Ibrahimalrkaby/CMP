<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'description',
    ];

    /**
     * The courses  that belong to the semester.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }
}
