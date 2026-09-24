<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Vendor extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = "vendors";
    protected $guarded = [];


    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
}
