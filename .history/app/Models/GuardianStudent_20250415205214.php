<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuardianStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'national_id',
        'email',
        'phone',
        'city',
    ];

    public function StudentData(): BelongsTo
    {
        return $this->belongsTo(StudentData::class);
    }
}
