<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'file_path', 'section_id'];

    // Un documento pertenece a una sección
    public function section()
    {
        return $this->belongsTo(DocumentSection::class);
    }
}
