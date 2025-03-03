<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 🔹 Importar HasApiTokens

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // 🔹 Agregar HasApiTokens aquí

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function difficultyScores()
    {
        return $this->hasMany(DifficultyScore::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
