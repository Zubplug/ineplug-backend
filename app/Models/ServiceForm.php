<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceForm extends Model
{
    protected $fillable = ['service_id', 'fields'];

    protected $casts = [
        'fields' => 'array', // Automatically convert JSON to array and back
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
