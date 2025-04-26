<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'program_id',
    ];

    // One teacher has many students
    public function students()
    {
        return $this->hasMany(StudentData::class, 'supervisor_id', 'id');
    }

    /**
     * Get the post that owns the comment.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
