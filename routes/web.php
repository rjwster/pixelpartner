<?php

use App\Helpers\Idea;
use App\Helpers\SwiperIdea;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MindMapController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/create', [ChatController::class, 'store'])->name('chat.store');

    // MindMap
    Route::resource('mindmap', MindMapController::class)->except(['destroy']);
    Route::get('/mindmap/{mindmap}/json', [MindMapController::class, 'mindmapJson'])->name('mindmap.json');
    
    Route::post('/mindmap/store-idea', [Idea::class, 'storeIdea'])->name('mindmap.store-idea');
    Route::post('/mindmap/generate-aidea', [Idea::class, 'generateAidea'])->name('mindmap.generate-aidea');
    Route::post('/mindmap/generate-swiper', [Idea::class, 'generateSwiper'])->name('mindmap.generate-swiper');
    Route::post('/mindmap/update-ideas', [Idea::class, 'updateIdeas'])->name('mindmap.update-ideas');

    // Swiper
    Route::post('/swiper/{swiperIdea}/accept', [SwiperIdea::class, 'acceptIdea'])->name('swiper.accept');
});

require __DIR__.'/auth.php';
