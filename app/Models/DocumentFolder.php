<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class DocumentFolder extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'section_id', 'parent_id'];

    // 📁 Carpeta padre
    public function parent()
    {
        return $this->belongsTo(DocumentFolder::class, 'parent_id');
    }

    // 📂 Subcarpetas
    public function children()
    {
        return $this->hasMany(DocumentFolder::class, 'parent_id');
    }

    // 🔁 Subcarpetas recursivas
    public function childrenRecursive()
    {
        return $this->hasMany(DocumentFolder::class, 'parent_id')->with('childrenRecursive');
    }

    // 🔗 Sección (FMVB, FIVB…)
    public function section()
    {
        return $this->belongsTo(DocumentSection::class);
    }

    // 📄 Documentos que contiene esta carpeta
    public function documents()
    {
        return $this->hasMany(Document::class, 'folder_id');
    }

    public function buildPath()
    {
        $segments = [];
        $current = $this;

        while ($current) {
            array_unshift($segments, Str::slug($current->name));
            $current = $current->parent;
        }

        $section = strtolower($this->section->name);
        return $section . '/' . implode('/', $segments);
    }
}
