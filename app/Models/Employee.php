<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Employee extends Model implements AuditableContract
{
    use HasFactory,Auditable;

    protected $table = "employee";
    protected $guarded = [];

    public function division()
    {
        return $this->belongsTo(Division::class,'division_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function report()
    {
        return $this->belongsTo(User::class, 'report_to');
    }
    public function department()
    {
        return $this->belongsTo(Department::class,'department_id');
    }
    public function superdepartment()
    {
        return $this->belongsTo(SupDept::class,'suptdepart_id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class,'location_id');
    }
    public function design()
    {
        return $this->belongsTo(Designation::class,'designation');
    }
    // public function employee()
    // {
    //     return $this->hasMany(Asset::class);
    // }
}
