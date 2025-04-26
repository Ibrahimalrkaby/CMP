<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function teachers(): HasMany
    {
        return $this->hasMany(TeacherData::class);
    }
}

