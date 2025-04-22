<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'subcategory',
        'price',
        'commission',
        'access_right',
        'service_type',
        'parent_id'
    ];

    public function children()
    {
        return $this->hasMany(Service::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Service::class, 'parent_id');
    }

    public function form()
    {
        return $this->hasOne(ServiceForm::class);
    }
}
