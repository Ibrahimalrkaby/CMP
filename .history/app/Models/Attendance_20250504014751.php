<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['lecture_id', 'student_id', 'present'];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function students()
    {
        return $this->hasMany(StudentData::class, 'student_id');
    }
}
