<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class course extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'department',
        'level',
        'credit_hours',
        'grade',
        'student_id',
        'semester_id',
    ];

    /**
     * The semesters that belong to the course.
     */
    public function semesters(): BelongsToMany
    {
        return $this->belongsToMany(Semester::class);
    }

    /**
     * The students that belong to the course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(StudentData::class);
    }
}
