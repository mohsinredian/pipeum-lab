<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NVNotes extends Model
{
    use HasFactory;
    protected $table = "notes";
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class,'dept_id');
    }
  
}
