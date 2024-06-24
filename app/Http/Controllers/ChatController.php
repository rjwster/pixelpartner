<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::where('user_id', auth()->id())->get();
        $activeChat = Chat::find(request('chat'));

        return view('chat.index', [
            'chats' => $chats,
            'activeChat' => $activeChat,
        ]);
    }

    public function store()
    {
        $chat = Chat::create([
            'user_id' => auth()->id(),
            'name' => 'Chat ' . date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('chat.index', [
            'chat' => $chat,
        ]);
    }
}
