<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailbox extends Model
{
    use HasFactory;
    protected $table = 'mailbox';

    protected $fillable = [
        'sender_id',
        'sender_type',
        'student_id',
        'subject',
        'message',
        'is_read',
    ];

    public function student()
    {
        return $this->belongsTo(StudentData::class, 'student_id');
    }
}
