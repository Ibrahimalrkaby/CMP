<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id',
        'student_id',
        'status',
        'grade'              // need it gor pass filter 
    ];
    
    public $timestamps = false;
}
