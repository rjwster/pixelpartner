<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MindMapIdea extends Model
{
    use HasFactory;

    public $table = 'mindmap_ideas';
    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'mindmap_id',
        'idea',
        'idea_type',
        'image_path',
        'accepted',
        'dir',
        'loc',
    ];

    public function mindmap()
    {
        return $this->belongsTo(MindMap::class);
    }

    public function parent()
    {
        return $this->belongsTo(MindMapIdea::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MindMapIdea::class, 'parent_id');
    }
}
