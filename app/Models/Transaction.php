<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',             // vtu, service, transfer
        'category',         // airtime, electricity, waec, etc.
        'reference',        // unique transaction ID or VTpass request_id
        'status',           // pending, successful, failed
        'amount',
        'recipient',        // phone, meter, smartcard, or account number
        'metadata',         // optional payload or API response
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Link to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
