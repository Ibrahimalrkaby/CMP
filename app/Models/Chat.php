<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'teacher_id',
        'course_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherData::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
