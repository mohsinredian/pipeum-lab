<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = "inventory";
    protected $guarded = [];

    public function inventory_serial_number_mapping()
    {
        return $this->hasMany(InventorySerialNumberMapping::class, 'inventory_id');
    }
    public function asset()
    {
        return $this->belongsTo(Asset::class,'item_type');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }
     public function brand()
     {
        return $this->belongsTo(Brand::class,'brand_id');
    }
    public function asset_type()
    {
        return $this->belongsTo(Asset::class,'asset_type');
    }
}
