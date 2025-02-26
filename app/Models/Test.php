<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'difficulty', 'category_id'];

    // Un test pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Un test puede tener muchos intentos
    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }
}
