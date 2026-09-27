<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otps extends Model
{
    use HasFactory;
    protected $tabel = 'otps';
    protected $fillable = [
        'user_id',
        'otp',
        'used_at',
        'expiry',
        'type',
    ];

    protected $casts = [
        'expiry' => 'datetime',
    ];

    /**
     * Get the user that owns the OTP record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
