<?php

use Illuminate\Support\Facades\Route;
use link0\Finder\Http\Controllers\FinderController;

Route::get('/', [FinderController::class, 'show'])->name('index');
Route::post('/search', [FinderController::class, 'search'])->name('search');
