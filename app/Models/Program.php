<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',

        'description',
    ];

    public function teachers(): HasMany
    {
        return $this->hasMany(TeacherData::class);
    }
        'teacher_id',
        'student_id',
    ];


    /**
     * Get the teacher data for the program.
     */
    public function teacherData(): HasMany
    {
        return $this->hasMany(TeacherData::class);
    }

    /**
     * Get the student data for the program.
     */
    public function studentData(): HasMany
    {
        return $this->hasMany(StudentData::class);
    }

}

