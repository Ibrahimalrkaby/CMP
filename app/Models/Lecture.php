<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'teacher_id',
        'course_id',
        'table_name',
        'start_time',
        'end_time'
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherData::class);
    }

    // app/Models/Attendance.php
    // public function student()
    // {
    //     return $this->belongsTo(StudentData::class, 'student_id', 'student_id');
    // }

    // app/Models/Lecture.php
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
