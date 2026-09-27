<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotesStatus extends Model
{
    use HasFactory;
    protected $table = "notes_status";
    protected $guarded = []; 

    public function notes()
    {
        return $this->belongsTo(NVNotes::class,'notes_id');
    }
}
