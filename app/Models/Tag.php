<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color'];

    // Una etiqueta puede estar en muchas preguntas (muchos a muchos)
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'question_tag');
    }
}
