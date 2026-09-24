<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpexBudget extends Model
{
    use HasFactory;
    protected $table = "opex_budget";
    protected $guarded = [];
}
