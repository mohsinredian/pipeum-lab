<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialDoc extends Model
{
    use HasFactory;
    protected $table = "tbl_material_doc";
    protected $guarded = [];
    public function department()
    {
        return $this->belongsTo(Department::class,'dept_id');
    }
}
