<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class UserModel extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'email_verified_at', 'remember_token',  'created_at', 'updated_at'];
    protected $primaryKey = 'id';
}
