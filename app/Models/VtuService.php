<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VtuService extends Model
{
    protected $fillable = [
        'service_id',
        'variation_code',
        'name',
        'amount',
        'agent_commission',
        'aggregator_commission',
        'active',
    ];
}
