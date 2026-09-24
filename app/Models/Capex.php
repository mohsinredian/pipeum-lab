<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Capex extends Model implements AuditableContract
{
    use HasFactory,Auditable;
    protected $table = "capex_master";
    protected $guarded = [];

    public function superdep()
    {
        return $this->belongsTo(SupDept::class,'super_department');
    }
    public function dep()
    {
        return $this->belongsTo(Department::class,'department_id');
    }

}
