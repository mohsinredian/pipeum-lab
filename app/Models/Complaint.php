<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $table = "complaint";
    protected $guarded = [];


    public function department()
    {
        return $this->belongsTo(Department::class,'department_id');
    }

    public function circle()
    {
        return $this->belongsTo(Circle::class,'circle_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class,'location_id');
    }
    public function asset()
    {
        return $this->belongsTo(Asset::class,'asset_id');
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class,'vendor_id');
    }


}
