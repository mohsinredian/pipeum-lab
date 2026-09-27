<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clarification extends Model
{
    use HasFactory;
    protected $table = "clarification_log";
    protected $guarded = [];
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
}
