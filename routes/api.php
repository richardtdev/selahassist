<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlanController; // Import PlanController
use App\Http\Controllers\Api\TeamSubscriptionController; // Import TeamSubscriptionController

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route for listing active subscription plans
Route::middleware('auth:sanctum')->get('/plans', [PlanController::class, 'index']);

// Route for creating a new team subscription
Route::middleware('auth:sanctum')->post('/teams/{team}/subscriptions', [TeamSubscriptionController::class, 'store'])->name('teams.subscriptions.store');

// Route for cancelling a team's subscription
Route::middleware('auth:sanctum')->delete('/teams/{team}/subscriptions', [TeamSubscriptionController::class, 'destroy'])->name('teams.subscriptions.destroy');

// Route for redirecting to Stripe Billing Portal
Route::middleware('auth:sanctum')->get('/teams/{team}/billing-portal', [TeamSubscriptionController::class, 'billingPortal'])->name('teams.billing-portal');
