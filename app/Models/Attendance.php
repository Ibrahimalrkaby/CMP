<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = ['lecture_id', 'student_id', 'present'];
=======
    protected $fillable = ['course_id', 'lecture_id', 'student_id', 'present'];

    public function students()
    {
        return $this->belongsToMany(StudentData::class, 'attendance_student')
            ->withPivot('present')
            ->withTimestamps();
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
>>>>>>> da85b30997a9f549c26d237af080612837864fda

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }
<<<<<<< HEAD

    public function student()
    {
        return $this->belongsTo(StudentData::class, 'student_id', 'student_id');
    }
=======
>>>>>>> da85b30997a9f549c26d237af080612837864fda
}
