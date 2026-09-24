<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Opex extends Model implements AuditableContract
{
    use HasFactory,Auditable;
    protected $table = "opex";
   protected $guarded = [];

   public function department()
    {
        return $this->belongsTo(Department::class,'department_id');
    }
    public function subdepartment()
    {
        return $this->belongsTo(Department::class,'sub_department');
    }
    public function superdep()
    {
        return $this->belongsTo(SupDept::class,'super_department');
    }
}
