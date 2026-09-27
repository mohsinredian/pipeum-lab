<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory; 
    protected $table = "tickets";
    protected $guarded = [];

    public function division()
    {
        return $this->belongsTo(Division::class,'company_id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class,'location_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class,'task_name','id');
    }
    public function assignee()
    {
        return $this->belongsTo(User::class,'assigne_id','id');
    }
}
