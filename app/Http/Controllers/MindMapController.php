<?php

namespace App\Http\Controllers;

use App\Helpers\OpenAi;
use App\Models\MindMap;
use App\Models\MindMapIdea;
use Illuminate\Http\Request;

class MindMapController extends Controller
{
    public function index()
    {
        $mindmaps = MindMap::withCount('ideas')->orderBy('id', 'DESC')->get();

        return view('mindmap.index', [
            'mindmaps' => $mindmaps,
        ]);
    }

    public function create()
    {
        return view('mindmap.create');
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => 'required|string',
        ]);

        $mindmap = MindMap::create($attributes);

        $mindmap->ideas()->create([
            'idea' => $mindmap->name,
            'idea_type' => 'user',
        ]);

        return redirect()->route('mindmap.show', $mindmap);
    }

    public function show(MindMap $mindmap)
    {
        $mindmap->load('ideas');

        $mindMapJSON = $this->buildMindmapJSON($mindmap->id);

        return view('mindmap.show', [
            'mindmap' => $mindmap,
            'mindMapJSON' => $mindMapJSON,
        ]);
    }

    public function mindmapJson(MindMap $mindmap)
    {
        return $this->buildMindmapJSON($mindmap->id);
    }

    private function buildMindmapJSON($mindmapId) {
        $mindmap = Mindmap::findOrFail($mindmapId);
        $ideas = $mindmap->ideas()->get();
        $nodes = [];
    
        foreach ($ideas as $idea) {
            $nodes[] = $this->buildNode($idea);
        }
    
        return json_encode([
            "class" => "go.TreeModel",
            "nodeDataArray" => $nodes
        ]);
    }

    private function buildNode($idea) {
        $node = [
            "key" => $idea->id,
            "text" => $idea->idea,
            "loc" => $idea->loc,
            "brush" => "white",
            "textColor" => "white",
        ];

        if (isset($idea->image_path) && !empty($idea->image_path)) {
            $node['source'] =  '../' . $idea->image_path;
        }

        if (isset($idea->parent_id)) {
            $node['parent'] = $idea->parent_id;
        }

        return $node;
    }
}
