<?php

namespace App\Helpers;

use App\Models\MindMap;
use App\Models\MindMapIdea;
use App\Models\SwiperIdeas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Idea
{
    /**
     * Generate idea using OpenAI
     * @param string $text
     * @return string
     */
    public function storeIdea(Request $request): string
    {
        $requestData = $request->all();
        if (!isset($requestData['mindmap_id']) || empty($requestData['mindmap_id'])) {
            return response()->json(['error' => 'MindMap ID is required']);
        }

        if (!isset($requestData['data']) || empty($requestData['data'])) {
            return response()->json(['error' => 'Data is required']);
        }

        $mindmap = MindMap::find($requestData['mindmap_id']);
        if (!$mindmap) {
            return response()->json(['error' => 'MindMap not found']);
        }

        if (!isset($requestData['data']['text'], $requestData['data']['parent'], $requestData['data']['loc'])) {
            return response()->json(['error' => 'Data is not complete']);
        }

        $idea = $mindmap->ideas()->create([
            'parent_id' => $requestData['data']['parent'],
            'idea' => $requestData['data']['text'],
            'idea_type' => 'user',
            'loc' => $requestData['data']['loc'],
        ]);

        return response()->json([
            'success' => true,
            'key' => $idea->id,
        ])->getContent();
    }

    /**
     * Generate idea using OpenAI
     * @param string $text
     * @return string
     */
    public function generateAidea(Request $request): string
    {
        $requestData = $request->all();
        if (!isset($requestData['text']) || empty($requestData['text'])) {
            return response()->json(['error' => 'Text is required']);
        }

        if (!isset($requestData['mindmap_id']) || empty($requestData['mindmap_id'])) {
            return response()->json(['error' => 'Mindmap ID is required']);
        }

        if (!isset($requestData['parent_id']) || empty($requestData['parent_id'])) {
            return response()->json(['error' => 'Parent ID is required']);
        }

        $currentIdeas = MindMapIdea::where('mindmap_id', $requestData['mindmap_id'])->pluck('idea')->toArray();
        $idea = OpenAi::generateIdea($requestData['text'], $currentIdeas);

        $mindMapIdea = MindMapIdea::create([
            'mindmap_id' => $requestData['mindmap_id'],
            'parent_id' => $requestData['parent_id'],
            'idea' => $idea,
            'idea_type' => 'ai',
        ]);

        if (!isset($idea) || empty($idea)) {
            return response()->json(['error' => 'No response from OpenAI']);
        }

        return response()->json([
            'idea' => $mindMapIdea->idea,
            'key' => $mindMapIdea->id,
        ])->getContent();
    }

    /**
     * Update ideas
     * @param Request $request
     * @return string
     */
    public function updateIdeas(Request $request): string
    {
        $requestData = $request->all();
        if (!isset($requestData['mindmap_id']) || empty($requestData['mindmap_id'])) {
            return response()->json(['error' => 'MindMap ID is required']);
        }

        if (!isset($requestData['data']) || empty($requestData['data'])) {
            return response()->json(['error' => 'MindMap data is required']);
        }

        $ideas = json_decode($requestData['data'], true);
        if (isset($ideas['nodeDataArray'])) {
            foreach ($ideas['nodeDataArray'] as $idea) {
                $dataToUpdate = [
                    'loc' => $idea['loc'],
                    'idea' => $idea['text'],
                ];
    
                if (isset($idea['parent'])) {
                    $dataToUpdate['parent_id'] = $idea['parent'];
                }
    
                MindMapIdea::where('mindmap_id', $requestData['mindmap_id'])->where('id', $idea['key'])->update($dataToUpdate);
            }
        }

        // collect the keys and sort them in descending order so that we delete the children first
        $keys = collect($ideas['nodeDataArray'])->pluck('key')->toArray();
        $items = MindMapIdea::query()
            ->where('mindmap_id', $requestData['mindmap_id'])
            ->whereNotIn('id', $keys)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($items as $item) {
            $item->delete();
        }

        return response()->json([
            'success' => true,
        ])->getContent();
    }

    /**
     * Generate swiper
     * @param MindMap $mindmap
     * @return void
     */
    public function generateSwiper(Request $request)
    {
        $requestData = $request->all();
        if (!isset($requestData['mindmap_id']) || empty($requestData['mindmap_id'])) {
            return response()->json(['error' => 'MindMap ID is required']);
        }

        if (!isset($requestData['parent_id']) || empty($requestData['parent_id'])) {
            return response()->json(['error' => 'parent_id is required']);
        }

        $mindmap = MindMap::find($requestData['mindmap_id']);
        if (!$mindmap) {
            return response()->json(['error' => 'MindMap not found']);
        }

        $groupNumber = SwiperIdeas::getHighestGroupNumber($mindmap->id) + 1;
        
        $existingIdeas = MindMapIdea::where('mindmap_id', $mindmap->id)->pluck('idea')->toArray();
        $selectedIdea = $requestData['text'];
        
        // Version 1
        // for ($i = 0; $i < 5; $i++) {
        //     $this->getIdea($mindmap->id, $requestData['parent_id'], $groupNumber, $existingIdeas, $selectedIdea);
        // }

        // Version 2
        $this->getIdeas($mindmap->id, $requestData['parent_id'], $groupNumber, $existingIdeas, $selectedIdea);

        // get all generated ideas
        $generatedIdeas = SwiperIdeas::where('mindmap_id', $mindmap->id)->where('group_number', $groupNumber)->get();

        // return the view with the swiper component
        return view('components.swiper', ['generatedIdeas' => $generatedIdeas])->render();
    }

    private function getIdea($mindmapId, $parentId, $groupNumber, &$existingIdeas, $selectedIdea)
    {
        $idea = OpenAi::generateIdea($selectedIdea, $existingIdeas);
        
        $existingIdeas[] = $idea;
        
        $prompt = OpenAi::generatePrompt($idea, $existingIdeas);
        $image = OpenAi::getImage($prompt);

        $imageName = Str::slug($idea) . '-' . uniqid() . '.jpg';
        $imagePath = 'images/generated/' . $mindmapId . '/' . $groupNumber . '/' . $imageName;

        $swiperIdea = SwiperIdeas::create([
            'mindmap_id' => $mindmapId,
            'parent_id' => $parentId,
            'group_number' => $groupNumber,
            'idea' => $idea,
            'image_prompt' => $prompt,
            'revised_prompt' => $image['revised_prompt'],
            'image_path' => 'storage/' . $imagePath,
            'open_ai_image_url' => $image['url'],
        ]);

        try {
            mkdir(storage_path('app/public/images/generated/' . $mindmapId . '/' . $groupNumber), 0755, true);
            Storage::put('public/' . $imagePath, file_get_contents($image['url']));
        } catch (\Exception $e) {
            // if the image is not saved in the storage folder, remove the image_path from the swiperIdea.
            $swiperIdea->update(['image_path' => null]);
        }
    }

    private function getIdeas($mindmapId, $parentId, $groupNumber, &$existingIdeas, $selectedIdea)
    {
        $imagePrompts = [];

        $ideas = OpenAi::generateIdeaV2($selectedIdea, $existingIdeas);

        $imagePrompts = OpenAi::generatePrompts($ideas, $existingIdeas);

        $images = OpenAi::getImages($imagePrompts);

        if (!file_exists(storage_path('app/public/images/generated/' . $mindmapId . '/' . $groupNumber)) && !empty($images)) {
            mkdir(storage_path('app/public/images/generated/' . $mindmapId . '/' . $groupNumber), 0755, true);
        }

        // bulk save images and ideas
        foreach ($images as $key => $image) {
            $imageName = Str::slug($ideas[$key]) . '-' . uniqid() . '.jpg';
            $imagePath = 'images/generated/' . $mindmapId . '/' . $groupNumber . '/' . $imageName;

            $swiperIdea = SwiperIdeas::create([
                'mindmap_id' => $mindmapId,
                'parent_id' => $parentId,
                'group_number' => $groupNumber,
                'idea' => $ideas[$key],
                'image_prompt' => $imagePrompts[$key],
                'revised_prompt' => $image['revised_prompt'],
                'image_path' => 'storage/' . $imagePath,
                'open_ai_image_url' => $image['url'],
            ]);

            try {
                Storage::put('public/' . $imagePath, file_get_contents($image['url']));
            } catch (\Exception $e) {
                // if the image is not saved in the storage folder, remove the image_path from the swiperIdea.
                $swiperIdea->update(['image_path' => null]);
            }
        }
    }
}