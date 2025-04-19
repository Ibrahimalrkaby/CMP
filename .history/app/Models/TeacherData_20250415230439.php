<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherData extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'department',
        'personal_id',
        'rank',
    ];

    // One teacher has many students
    public function students()
    {
        return $this->hasMany(StudentData::class, 'supervisor_id', 'id');
    }
}
