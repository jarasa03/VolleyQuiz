<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Una categoría tiene muchas preguntas
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Una categoría tiene muchos tests
    public function tests()
    {
        return $this->hasMany(Test::class);
    }
}
