<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapexBudget extends Model
{
    use HasFactory;
    protected $table = "capex_budget";
    protected $guarded = [];
}
