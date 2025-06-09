<?php

use Illuminate\Support\Facades\Route;

Route::prefix('media')
    ->group(function () {
        Route::get('/', \MediaManager\Http\Livewire\MediaManager::class)->name('media-manager.index');
    }); 