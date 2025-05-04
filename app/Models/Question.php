<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question_text', 'question_type', 'category_id'];

    // Una pregunta pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Una pregunta puede tener muchas respuestas
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    // Una pregunta puede tener muchas etiquetas (muchos a muchos)
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'question_tag');
    }

    public function explanation()
    {
        return $this->hasOne(Explanation::class);
    }
}
