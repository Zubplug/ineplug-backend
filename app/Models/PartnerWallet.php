<?php

// File: app/Models/PartnerWallet.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerWallet extends Model
{
    protected $fillable = ['partner_id', 'balance'];

    public function partner()
    {
        return $this->belongsTo(PartnerStaff::class, 'partner_id');
    }
}

