<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuardianStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id',
        'email',
        'phone',
        'city',
    ];

    // Ensure the table name matches your migration
    protected $table = 'students_guardian';

    // Inverse relationship: GuardianStudent HAS ONE StudentData
    public function studentData(): HasOne
    {
        return $this->hasOne(StudentData::class, 'guardian_id', 'national_id');
    }
}
