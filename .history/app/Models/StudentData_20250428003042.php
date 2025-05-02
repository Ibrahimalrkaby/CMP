<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentData extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'department',
        'personal_id',
        'guardian_id',
    ];

    // Relationship to PersonalData (StudentData BELONGS TO PersonalData)
    public function personalData()
    {
        return $this->belongsTo(PersonalData::class, 'personal_id', 'id');
    }

    // Relationship to GuardianStudent (StudentData BELONGS TO GuardianStudent)
    public function guardianData()
    {
        return $this->belongsTo(GuardianStudent::class, 'guardian_id', 'national_id');
    }

    /**
     * The courses that belong to the student.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    /**
     * The supervisor that belongs to the student.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(TeacherData::class, 'supervisor_id', 'id');
    }

    /**
     * The program that belongs to the student.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
