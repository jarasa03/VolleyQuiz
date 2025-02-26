<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DifficultyScore extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'test_id', 'difficulty', 'score'];

    // Una puntuación pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Una puntuación pertenece a un test
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
