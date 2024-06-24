<?php

namespace App\Helpers;

use App\Models\SwiperIdeas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SwiperIdea
{
    public function acceptIdea(SwiperIdeas $swiperIdea)
    {
        $swiperIdea->update([
            'accepted' => true,
        ]);

        if (!$swiperIdea->image_path || !file_exists(storage_path('app/public/' . $swiperIdea->image_path))) {
            if (!file_exists(storage_path('app/public/images/generated/' . $swiperIdea->mindmap_id . '/' . $swiperIdea->group_number))) {
                mkdir(storage_path('app/public/images/generated/' . $swiperIdea->mindmap_id . '/' . $swiperIdea->group_number), 0755, true);
            }

            $imageName = Str::slug($swiperIdea->idea) . '-' . uniqid() . '.jpg';
            $imagePath = 'images/generated/' . $swiperIdea->mindmap_id . '/' . $swiperIdea->group_number . '/' . $imageName;
            $swiperIdea->update([
                'image_path' => 'storage/' . $imagePath,
            ]);

            Storage::put('public/' . $imagePath, file_get_contents($swiperIdea->open_ai_image_url));
        }

        $swiperIdea->mindmap->ideas()->create([
            'parent_id' => $swiperIdea->parent_id,
            'mindmap_id' => $swiperIdea->mindmap_id,
            'idea' => $swiperIdea->idea,
            'idea_type' => 'ai',
            'image_path' => $swiperIdea->image_path,
        ]);

        return response()->json(['success' => 'Idea accepted']);
    }
}