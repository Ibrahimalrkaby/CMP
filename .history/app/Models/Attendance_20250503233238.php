<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function setTableName($tableName)
    {
        $this->table = $tableName;
        return $this;
    }
}
