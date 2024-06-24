<div class="chat">
    <div class="messages-chat max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @foreach ($messages as $message)
            @if ($message->type == 'response')
                <div class="message">
                    <div class="response">
                        <p class="text">{{ $message->body }}</p>
                    </div>
                </div>
            @else
                <div class="message">
                    <p class="text">{{ $message->body }}</p>
                </div>
            @endif
        @endforeach
    </div>
    <div class="footer-chat">
        <form wire:submit.prevent="sendMessage" class="write-message">
            <input id="message-input" type="text" placeholder="Type your message here" wire:model="newMessage" @disabled(!$activeChat) />
            <button type="submit">Send</button>
        </form>
    </div>
</div>
