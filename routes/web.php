<?php

use Illuminate\Support\Facades\Route;
use Link000\Finder\Http\Controllers\Finder\FinderController;

// TODO: routes must be published as part of stack install process
// api routes unlinke others, dont have a show/index route

Route::get('/', [FinderController::class, 'show'])->name('index');
Route::post('/search', [FinderController::class, 'search'])->name('search');
