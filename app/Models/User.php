<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'name',
        'email',
        'phone',
        'gender',
        'dob',
        'address',
        'referral_code',
        'transaction_pin',
        'password',
        'wallet_balance',
        'role',
        'kyc_level',
        'bvn',
        'nin',
        'virtual_account_number',
        'virtual_account_bank',
        'account_reference',
        'pnd',
        'address_verified',
    ];

    protected $hidden = [
        'password',
        'transaction_pin',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function addressVerification()
    {
        return $this->hasOne(AddressVerification::class);
    }
}
