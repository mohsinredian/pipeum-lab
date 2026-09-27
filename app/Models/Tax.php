<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Division;
use App\Models\Department;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Tax extends Model implements AuditableContract
{
    use HasFactory,Auditable;
    
    protected $table = "tax";
    protected $guarded = [];

    public function department(){
        return $this->belongsTo(Department::class ,'department_id');
    }
    public function division(){
        return $this->belongsTo(Division::class ,'company_id');
    }
}
