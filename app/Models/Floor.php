<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $table = "floors";
    protected $guarded = [];

    public function location()
    {
        return $this->belongsTo(Location::class,'location_id');
    }
}
