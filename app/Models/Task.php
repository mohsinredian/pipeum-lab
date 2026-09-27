<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = "taks";
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
    public function employee()
    {
        return $this->belongsTo(Employee::class,'assigne_id');
    }
    public function assignee()
    {
        return $this->belongsTo(User::class,'assigne_id','id');
    }
    
}
