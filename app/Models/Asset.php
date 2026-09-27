<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = "asset";
    protected $guarded = [];

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

}