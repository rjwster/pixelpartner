<?php

namespace App\Livewire;

use App\Helpers\OpenAi;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $messages = [];
    public $newMessage;
    public $user;
    public $chat;
    public $activeChat;

    public function mount()
    {
        $this->user = Auth::user();

        if ($this->activeChat) {
            $this->chat = $this->activeChat;
        }

        $this->getMessages();
    }

    public function sendMessage()
    {
        if (empty($this->newMessage)) {
            return;
        }

        $message = Message::create([
            'chat_id' => $this->chat->id,
            'user_id' => $this->user->id,
            'type' => 'question',
            'body' => $this->newMessage,
        ]);

        $response = OpenAi::chat([
            [
                'role' => 'system',
                'content' => 'User: ' . $this->newMessage,
            ],
        ]);

        Message::create([
            'chat_id' => $this->chat->id,
            'user_id' => null,
            'response_id' => $message->id,
            'type' => 'response',
            'body' => $response['choices'][0]['message']['content'],
        ]);

        $this->newMessage = '';
    }

    public function render()
    {
        $this->getMessages();

        return view('livewire.chat');
    }

    public function getMessages()
    {
        if (isset($this->chat->id)) {
            $this->messages = Message::where('chat_id', $this->chat->id)->get();
        }
    }
}
