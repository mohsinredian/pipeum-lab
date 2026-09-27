<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventorySerialNumberMapping extends Model
{
    use HasFactory;

    protected $table = "inventory_serial_number_mapping";
    protected $guarded = [];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
