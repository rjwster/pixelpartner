<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MindMap extends Model
{
    use HasFactory;

    public $table = 'mindmaps';

    protected $fillable = [
        'name',
        'data',
    ];

    public function ideas()
    {
        return $this->hasMany(MindMapIdea::class, 'mindmap_id', 'id');
    }
}
