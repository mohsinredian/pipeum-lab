<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeedValidation extends Model
{
    use HasFactory;

    protected $table = "needvalidations";
    protected $guarded = [];

    public function division()
    {
        return $this->belongsTo(Division::class,'company_id');
    }
  
    public function service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function material()
    {
        return $this->belongsTo(NVMaterial::class,'id','nv_id')->orderBy('id','desc');
    }
    public function services()
    {
        return $this->belongsTo(NVService::class,'id','nv_id')->orderBy('id','desc');
    }

    // For department id 18-03-2026
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}