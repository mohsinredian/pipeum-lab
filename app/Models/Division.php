<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Division extends Model implements AuditableContract
{
    use HasFactory,Auditable;

    protected $table = "divisions";
    protected $guarded = [];

    public function location()
    {
        return $this->hasOne(Location::class,'id','divisions_id');
    }
}
