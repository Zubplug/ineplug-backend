<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id',
        'name',
        'slug',
        'type',
        'fee_type',
        'price',
        'commission_partner',
        'commission_agent',
        'commission_aggregator',
        'status'
    ];

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}
