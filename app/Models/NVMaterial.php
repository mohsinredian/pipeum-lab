<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NVMaterial extends Model
{
    use HasFactory;
    protected $table = "tbl_material";
    protected $guarded = [];

    public function division()
    {
        return $this->belongsTo(Division::class,'company_id');
    }
  
    public function service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }
    // public function ref_num()
    // {
    //     return $this->belongsTo(NVMaterial::class,'nv_id');
    // }
    public function department()
    {
        return $this->belongsTo(Department::class,'dept_id');
    }
    public function nv()
    {
        return $this->hasMany(NeedValidation::class,'nv_id');
    }
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function nvstatus()
    {
        return $this->hasMany(Nvsericestatus::class,'nv_id');
    }
}
