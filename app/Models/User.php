<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // <- Importar la interfaz
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // IMPORTANTE: Agregar esta línea para Sanctum

class User extends Authenticatable implements MustVerifyEmail // <- Implementar la interfaz
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];

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

    // Método para verificar si un usuario es administrador
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
