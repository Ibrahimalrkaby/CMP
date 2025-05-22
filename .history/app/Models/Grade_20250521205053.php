<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'midterm_exam',
        'practical_exam',
        'oral_exam',
        'year_work',
        'final_grade',
        'total',
        'course_grade',
    ];

    public function student()
    {
        return $this->belongsTo(StudentData::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
