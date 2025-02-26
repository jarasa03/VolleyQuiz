<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSection extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Una sección tiene muchos documentos
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
