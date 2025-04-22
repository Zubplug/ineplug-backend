<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_service_id',
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

    public function mainService()
    {
        return $this->belongsTo(MainService::class);
    }

    public function subSubcategories()
    {
        return $this->hasMany(SubSubcategory::class);
    }
}
