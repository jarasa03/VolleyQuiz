<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'test_id', 'score', 'time_taken', 'streak'];

    // Un intento pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un intento pertenece a un test
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
