<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'department',
        'level',
        'credit_hours',
        'teacher_id'
    ];

    /**
     * The semesters that belong to the course.
     */
    public function semesters(): BelongsToMany
    {
        return $this->belongsToMany(Semester::class, 'course_semester');
    }

    /**
     * The students that belong to the course, with their grades.
     */
    public function student()
    {
<<<<<<< HEAD
        return $this->belongsToMany(StudentData::class, 'course_student')
                    ->withPivot('grade');
=======
        return $this->belongsTo(StudentData::class, 'student_id', 'student_id');
>>>>>>> 1f35d43 (Attendance)
    }

    /**
     * The schedules for the course
     */
    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }

     /**
     * Courses that are prerequisites for this course.
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class,'course_prerequisites','course_id','prerequisite_course_id');
    }
    /**
     * The schedule exam for the course
     */
    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherData::class, 'teacher_id', 'id');
    }



    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

<<<<<<< HEAD
=======
    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }
>>>>>>> 1f35d43 (Attendance)
}
