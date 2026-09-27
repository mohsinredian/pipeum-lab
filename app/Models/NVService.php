<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NVService extends Model
{
    use HasFactory;
    protected $table = "tbl_service";
    protected $guarded = []; 
    
    public function department(){
        return $this->belongsTo(Department::class,'dept_id');
    }

    public function needvalidations()
    {
        return $this->belongsTo(NeedValidation::class,'nv_id');
    }
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function nv()
    {
        return $this->hasMany(NeedValidation::class,'nv_id');
    }
    public function division()
    {
        return $this->belongsTo(Division::class,'company_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }
}
