<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan; // Import the Plan model
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the active subscription plans.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $plans = Plan::where('active', true)
                       ->get(['slug', 'name', 'price', 'features', 'trial_days']);
        
        return response()->json(['data' => $plans]);
    }
}
