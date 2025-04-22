<?php

// File: app/Models/RequestSubmission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'assigned_partner_id',
        'status',
        'admin_note',
        'admin_upload',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
    public function assignedPartner()
    {
        return $this->belongsTo(Partner::class, 'assigned_partner_id');
    }
    
