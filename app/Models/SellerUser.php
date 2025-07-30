<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class SellerUser extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'seller_users';

    protected $fillable = [
        'name', 'email', 'password', 'contact_number', 'profile_image',
        'business_name', 'business_address', 'city', 'state', 'pincode', 'role',
    ];

    protected $hidden = ['password'];
}