<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'day',
        'start_time',
        'end_time',
        'location',
        'type',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherData::class, 'teacher_id');
    }
}