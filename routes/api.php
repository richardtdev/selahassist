<?php

use App\Http\Controllers\SermonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/teams/{team}/sermons', [SermonController::class, 'store'])
    ->name('teams.sermons.store')
    ->middleware('auth:sanctum');
