<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemIssue extends Model
{
    use HasFactory;

    protected $table = "item_issue";
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
    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }
    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function employee(){
        return $this->belongsTo(Employee::class,'issued_to');
    }

    // public function itemmodel(){
    //     return $this->belongsTo(Inventory::class,'issued_to');
    // }

}
