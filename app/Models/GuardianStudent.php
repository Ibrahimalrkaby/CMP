<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuardianStudent extends Model
{
    use HasFactory;

    protected $table = 'students_guardian';
    protected $primaryKey = 'national_id';
    public $incrementing = false;
    
    protected $fillable = [
        'national_id',
        'email',
        'phone',
        'city',
    ];

    // Inverse relationship: GuardianStudent HAS ONE StudentData
    public function studentData()
    {
        return $this->hasOne(StudentData::class, 'guardian_id', 'national_id');
    }
}
