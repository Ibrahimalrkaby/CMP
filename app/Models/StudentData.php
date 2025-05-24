<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentData extends Model
{
    use HasFactory;

    protected $table = 'students_data';

    protected $fillable = [
        'student_id',
        'full_name',
        'email',
        'phone',
        'department',
        'personal_id',
        'guardian_id',
        'academic_year',
        'program_id',
        'gpa',
        'level',
        'total_credit_hours',
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


    // Fees
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    //
    public function registeredCourses()
    {
        return $this->belongsToMany(Course::class, 'course_registrations', 'student_id', 'course_id')
                    ->with(['schedules', 'teacher'])
                    ->withPivot('semester_id', 'status')
                    ->wherePivot('status', 'confirmed');

    }                

    public function user()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }



    // public function attendance()
    // {
    //     return $this->belongsTo(Attendance::class);
    // }

            

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }


    public function attendance()
    {
        return $this->belongsToMany(Attendance::class, 'attendance_student')
            ->withPivot('present')
            ->withTimestamps();
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
