<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'amount',
        'payment_date',
        'receipt_image',
    ];

    // Relation to Student model (one-to-many)
    public function student()
    {
        return $this->belongsTo(StudentData::class, 'student_id');
    }
}
