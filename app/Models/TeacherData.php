<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeacherData extends Model
{
    use HasFactory;
    protected $table = 'teacher_data';
    protected $fillable = [
        'phone',
        'department',
        'personal_id',
        'rank',
        'program_id',
        'role',
        'teacher_id',
    ];

    // One teacher has many students
    public function students()
    {
        return $this->hasMany(StudentData::class, 'supervisor_id', 'id');
    }

    /**
     * Get the program that belongs to the teacher.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

<<<<<<< HEAD
    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
=======

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
>>>>>>> da85b30997a9f549c26d237af080612837864fda
    }

    public function lecture()
    {
        return $this->hasMany(Lecture::class);
    }
<<<<<<< HEAD

    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id', 'national_id');
    }


=======
>>>>>>> da85b30997a9f549c26d237af080612837864fda
}
