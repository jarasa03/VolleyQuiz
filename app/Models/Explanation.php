<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Explanation extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'text', 'image_path'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
