<?php

/**
 * Nova-Sortable 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-sortable-5
 * Copyright (c) 2026 Almir Hodzic
 * MIT License
 */

use AlmirHodzic\NovaSortable5\Http\Controllers\SortController;
use Illuminate\Support\Facades\Route;

Route::post('/{resource}/reorder', [SortController::class, 'reorder'])
    ->name('nova-sortable.reorder');

Route::post('/{resource}/{resourceId}/move', [SortController::class, 'move'])
    ->name('nova-sortable.move');
