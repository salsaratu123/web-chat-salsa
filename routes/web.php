<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\ChatController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ChatController::class, 'index'])->name('dashboard');

    // Chat API routes
    Route::get('/messages/private/{user}', [ChatController::class, 'fetchPrivateMessages']);
    Route::get('/messages/group/{group}', [ChatController::class, 'fetchGroupMessages']);
    Route::post('/messages/private', [ChatController::class, 'sendPrivateMessage']);
    Route::post('/messages/group', [ChatController::class, 'sendGroupMessage']);
    
    // Group API routes
    Route::post('/groups', [ChatController::class, 'createGroup']);

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
