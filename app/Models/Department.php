<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Department extends Model implements AuditableContract
{
    use HasFactory,Auditable;

    protected $table = "department";
    protected $guarded = [];

    public function employee(){
        return $this->belongsTo(Employee::class ,'name');
    }
    public function deprew1(){
        return $this->belongsTo(Employee::class ,'name');
    }
    public function deprew2(){
        return $this->belongsTo(Employee::class ,'name');
    }
    public function deprew3(){
        return $this->belongsTo(Employee::class ,'name');
    }
    public function deprew4(){
        return $this->belongsTo(Employee::class ,'name');
    }
    public function groupcio(){
        return $this->belongsTo(Employee::class ,'name');
    }
  
  

}