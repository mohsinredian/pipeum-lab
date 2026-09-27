<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class MasterMaterialboq extends Model implements AuditableContract
{
    use HasFactory,Auditable;
    protected $table = "master_material_boq";
    protected $guarded = [];

   public function division()
    {
        return $this->belongsTo(Division::class,'company_id');
    }
  
}
