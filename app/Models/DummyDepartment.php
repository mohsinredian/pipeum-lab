<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DummyDepartment extends Model
{
    use HasFactory;
    protected $table = "dummy_department";
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class,'department_id');
    }
}
