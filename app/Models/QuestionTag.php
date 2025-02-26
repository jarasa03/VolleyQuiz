<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

// Laravel necesita una tabla intermedia entre questions y tags

class QuestionTag extends Pivot // Extiende de Pivot y no de Model, ya que es una tabla intermedia
{
    use HasFactory;

    protected $table = 'question_tag';
    protected $fillable = ['question_id', 'tag_id'];
}
