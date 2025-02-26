<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable // Extiende de Authenticatable en lugar de Model para la autenticación de usuarios de Laravel, se integra con su auth
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime']; // Laravel lo manejará como objeto Carbon para operaciones fecha/hora

    // Un usuario puede haber intentado varios tests
    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    // Un usuario puede tener puntuaciones por dificultad
    public function difficultyScores()
    {
        return $this->hasMany(DifficultyScore::class);
    }
}
