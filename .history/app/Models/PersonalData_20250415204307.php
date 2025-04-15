<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalData extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id',
        'age',
        'gender',
    ];

    public function studentData(): BelongsTo
    {
        return $this->belongsTo(StudentData::class);
    }
}
