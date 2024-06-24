<x-app-layout>

    <div class="grid grid-cols-[200px_minmax(900px,_1fr)]">
        <div>
            @foreach ($chats as $chat)
                <x-primary-link class="ms-3" href="{{ route('chat.index', ['chat' => $chat->id]) }}">
                    {{ __($chat->name) }}
                </x-primary-link>
            @endforeach

            <x-primary-link class="ms-3" href="{{ route('chat.store') }}">
                {{ __('Nieuwe chat') }}
            </x-primary-link>
        </div>
        <div>
            <livewire:chat :activeChat="$activeChat" />
        </div>
    </div>

</x-app-layout>
