<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalData extends Model
{
    use HasFactory;
    protected $table = 'students_personal_date';

    protected $fillable = [
        'national_id',
        'age',
        'gender',
    ];

    // Inverse relationship: PersonalData HAS ONE StudentData
    public function studentData(): HasOne
    {
        return $this->hasOne(StudentData::class, 'personal_id', 'id');
    }
}
