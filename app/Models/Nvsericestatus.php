<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nvsericestatus extends Model
{
    use HasFactory;
    protected $table = "nvservicestatus";
    protected $guarded = [];

    public function service()
    {
        return $this->belongsTo(NVService::class, 'service_id');
    }

    public function material()
    {
        return $this->belongsTo(NVMaterial::class, 'material_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}