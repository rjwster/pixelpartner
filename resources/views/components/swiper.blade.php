@props(['generatedIdeas'])
<div class="swiper-wrapper">
    <div class="swiper-header">
        <div class="header-title">
            <span>{{ __('Here are 5 ideas') }}</span>
            <span class="font-bold">{{ __('Like or reject') }}</span>
        </div>
    </div>
    
    <div class="swiper">
        <div class="swiper--status">
            <div class="icon-close">
                <div class="icon-wrapper">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                    </svg>
                </div>
            </div>
            <div class="icon-love">
                <div class="icon-wrapper">
                    <svg width="50" height="46" viewBox="0 0 50 46" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M47.8865 20.631C43.7091 28.706 32.5336 39.6878 26.2139 45.5377C25.8859 45.8353 25.4597 46 25.0179 46C24.576 46 24.1498 45.8353 23.8218 45.5377C17.4664 39.6878 6.29089 28.706 2.11348 20.631C-7.06256 2.86618 16.0739 -8.97704 25 8.7878C33.9261 -8.97704 57.0626 2.86618 47.8865 20.631Z" fill="white"/>
                    </svg>
                </div>
            </div>
        </div>
    
        <div class="swiper--cards">
            @foreach ($generatedIdeas as $generatedIdea)
                <x-swiper-card :generatedIdea="$generatedIdea" />
            @endforeach
        </div>
    </div>

    <div class="swiper-footer">
        <div class="footer-content">
            <span>{{ __('AI outputs can be misleading or wrong.') }}</span>
            <a href="#" class="btn btn-primary font-bold">{{ __('Learn more') }}</a>
        </div>
    </div>
</div>
