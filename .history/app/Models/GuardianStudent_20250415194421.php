<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardianStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id',
        'email',
        'phone',
        'city',
    ];


    public function data()
    {
        return $this->hasOne(User::class, 'guardian_id');
    }
}
