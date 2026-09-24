<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = "locations";
    protected $guarded = [];

    public function division()
    {
        return $this->belongsTo(Division::class,'divisions_id');
    }

    public function floor()
    {
        return $this->hasOne(Floor::class,'id','location_id');
    }
}
