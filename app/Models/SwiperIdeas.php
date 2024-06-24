<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SwiperIdeas extends Model
{
    use HasFactory, SoftDeletes;

    // fillable
    protected $fillable = [
        'mindmap_id',
        'parent_id',
        'group_number',
        'idea',
        'image_path',
        'open_ai_image_url',
        'image_prompt',
        'revised_prompt',
        'accepted',
    ];

    public static function getHighestGroupNumber($mindmapId): int
    {
        $highestGroupNumber = self::where('mindmap_id', $mindmapId)->max('group_number');
        return $highestGroupNumber ? $highestGroupNumber : 0;
    }

    public function mindmap(): BelongsTo
    {
        return $this->belongsTo(MindMap::class);
    }
}
