<x-app-layout>
    @vite(['resources/js/mindmap.js', 'resources/js/swiper.js'])

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">

        <div id="mindmap" class=""></div>
        
        <input type="hidden" id="mindmapId" value="{{ $mindmap->id }}">

        <textarea id="savedMindmapJSON" style="display: none;">{!! $mindMapJSON !!}</textarea>

    </div>

    <div id="cube-container" class="d-none">
        <div class="cube-wrapper">
            <div class="cube-folding">
                <span class="leaf1"></span>
                <span class="leaf2"></span>
                <span class="leaf3"></span>
                <span class="leaf4"></span>
            </div>
            <span class="loading" data-name="Loading">{{ __('Generating images') }}</span>
        </div>
    </div>

    <div
        id="swiper-modal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0"
    >
        <div id="swiper-container">
            {{-- Content will be inserted here with JavaScript --}}
        </div>
    </div>
</x-app-layout>