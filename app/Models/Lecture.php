<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $guarded = [];

<<<<<<< HEAD
=======
    protected $fillable = [
        'student_id',
        'teacher_id',
        'course_id',
        'table_name',
        'start_time'
    ];

>>>>>>> da85b30997a9f549c26d237af080612837864fda
    public function teacher()
    {
        return $this->belongsTo(TeacherData::class);
    }

    // app/Models/Attendance.php
    public function student()
    {
        return $this->belongsTo(StudentData::class, 'student_id', 'student_id');
    }

    // app/Models/Lecture.php
    public function attendances()
    {
<<<<<<< HEAD
        return $this->hasMany(Attendance::class);
=======
        return $this->hasOne(Attendance::class);
>>>>>>> da85b30997a9f549c26d237af080612837864fda
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
