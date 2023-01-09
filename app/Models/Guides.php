<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Guides extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $guard = "guide";

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'ktp_number',
        'ktp_url',
        'code',
        'address',
        'status',
        'profile',
        'car_photo',
        'car_type',
        'plat_number',
        'car_color',
        'bank_name',
        'bank_number',
        'bank_account',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function availabilities() {
        return $this->hasMany(Availability::class, 'guide_id', 'id');
    }

}
