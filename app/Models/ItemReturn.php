<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemReturn extends Model
{
    use HasFactory;

    protected $table = "item_return";
    protected $guarded = [];

    public function circle(){
        return $this->belongsTo(Circle::class,'circle_id');
    }
    public function location(){
        return $this->belongsTo(Location::class,'location_id');
    }
    
    public function asset(){
        return $this->belongsTo(Asset::class,'item_type');
    }
    public function returnlocation(){
        return $this->belongsTo(Location::class,'return_location');
    }
}
