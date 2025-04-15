<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function personalData()
    {
        return $this->belongsTo(PersonalData::class, 'personal_id');
    }

    public function guardianData()
    {
        return $this->belongsTo(GuardianStudent::class, 'guardian_id');
    }
}
