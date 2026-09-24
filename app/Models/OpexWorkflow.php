<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class OpexWorkflow extends Model implements AuditableContract
{
    use HasFactory,Auditable;
    protected $table = "opex_workflows";
    protected $guarded = [];



    public function workflow_dep(){
        return $this->belongsTo(Department::class ,'work_dep');
    }
    public function workflow_rew1(){
        return $this->belongsTo(Employee::class ,'name');
    }
    public function workflow_rew2(){
        return $this->belongsTo(Employee::class ,'name');
    }
    public function workflow_rew3(){
        return $this->belongsTo(Employee::class ,'name');
    }
    public function workflow_rew4(){
        return $this->belongsTo(Employee::class ,'name');
    }
    public function workflow_app(){
        return $this->belongsTo(Employee::class ,'name');
    }

  
    
}