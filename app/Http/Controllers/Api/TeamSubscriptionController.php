<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Added for logging

class TeamSubscriptionController extends Controller
{
    /**
     * Store a newly created subscription for the team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Team $team): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization: Check if the authenticated user owns or has 'owner' role on the team.
        // This is a common Jetstream authorization pattern.
        // Adjust if your authorization logic for 'managing' a team's subscription is different.
        if (!$user || $user->current_team_id !== $team->id || !$user->hasTeamRole($team, 'owner')) {
            Log::warning("Unauthorized attempt to manage subscription for team ID {$team->id} by user ID {$user->id}. User's current team ID: {$user->current_team_id}.");
            return response()->json(['message' => 'You are not authorized to manage subscriptions for this team.'], 403);
        }

        $validated = $request->validate([
            'plan_slug' => 'required|string|exists:plans,slug',
        ]);

        $plan = Plan::where('slug', $validated['plan_slug'])->where('active', true)->firstOrFail();

        Log::info("User ID {$user->id} attempting to subscribe team ID {$team->id} to plan '{$plan->name}' (Stripe ID: {$plan->stripe_plan_id}).");

        try {
            $checkoutSession = $team->newSubscription('default', $plan->stripe_plan_id)
                ->checkout([
                    'success_url' => url('/dashboard?subscription=success&session_id={CHECKOUT_SESSION_ID}'), // Placeholder URL
                    'cancel_url' => url('/dashboard?subscription=cancelled'), // Placeholder URL
                ]);
            
            Log::info("Stripe Checkout session created successfully for team ID {$team->id}. Session ID: {$checkoutSession->id}");
            return response()->json(['checkout_url' => $checkoutSession->url]);

        } catch (\Exception $e) {
            Log::error("Error creating Stripe Checkout session for team ID {$team->id}: " . $e->getMessage());
            return response()->json(['message' => 'Error creating subscription session: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cancel the team's active subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Team $team): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check
        if (!$user || $user->current_team_id !== $team->id || !$user->hasTeamRole($team, 'owner')) {
            Log::warning("Unauthorized attempt to cancel subscription for team ID {$team->id} by user ID {$user->id}. User's current team ID: {$user->current_team_id}.");
            return response()->json(['message' => 'You are not authorized to manage subscriptions for this team.'], 403);
        }

        Log::info("User ID {$user->id} attempting to cancel subscription for team ID {$team->id}.");

        try {
            $subscription = $team->subscription('default');

            if ($subscription && !$subscription->cancelled()) { // Check if there's a subscription and it's not already cancelled
                // $subscription->cancel(); // Cancels immediately
                $subscription->cancelAtPeriodEnd(); // Schedules cancellation at the end of the billing period
                Log::info("Subscription for team ID {$team->id} (Stripe ID: {$subscription->stripe_id}) scheduled for cancellation at period end.");
                return response()->json(['message' => 'Subscription scheduled for cancellation at the end of the current billing period.']);
            } elseif ($subscription && $subscription->onGracePeriod()) {
                 Log::info("Subscription for team ID {$team->id} (Stripe ID: {$subscription->stripe_id}) is already in grace period, ending on {$subscription->ends_at->toFormattedDateString()}.");
                return response()->json(['message' => 'Subscription is already in grace period and will cancel at period end.', 'ends_at' => $subscription->ends_at->toFormattedDateString()], 422);
            }
             elseif ($subscription && $subscription->cancelled()) {
                 Log::warning("Subscription for team ID {$team->id} (Stripe ID: {$subscription->stripe_id}) is already cancelled or ending.");
                return response()->json(['message' => 'Subscription is already cancelled or scheduled for cancellation.', 'ends_at' => $subscription->ends_at ? $subscription->ends_at->toFormattedDateString() : null], 422);
            }else {
                Log::warning("No active subscription found for team ID {$team->id} to cancel.");
                return response()->json(['message' => 'No active subscription to cancel.'], 404);
            }
        } catch (\Exception $e) {
            Log::error("Error cancelling subscription for team ID {$team->id}: " . $e->getMessage());
            return response()->json(['message' => 'Error cancelling subscription: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Redirect the user to the Stripe Billing Portal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\JsonResponse
     */
    public function billingPortal(Request $request, Team $team): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check
        if (!$user || $user->current_team_id !== $team->id || !$user->hasTeamRole($team, 'owner')) {
            Log::warning("Unauthorized attempt to access billing portal for team ID {$team->id} by user ID {$user->id}. User's current team ID: {$user->current_team_id}.");
            return response()->json(['message' => 'You are not authorized to access the billing portal for this team.'], 403);
        }

        Log::info("User ID {$user->id} requesting billing portal for team ID {$team->id}.");

        try {
            // Ensure the team is a Stripe customer. Cashier's billingPortal() method usually handles this.
            // If not, it might throw an exception or return null depending on Cashier version/config.
            // Explicitly creating a customer if one doesn't exist can be done before calling billingPortal()
            // $team->createOrGetStripeCustomer(); 
            
            $portalUrl = $team->billingPortal(url('/dashboard?billing_portal=returned')); // Placeholder return URL

            Log::info("Billing portal URL generated successfully for team ID {$team->id}.");
            return response()->json(['billing_portal_url' => $portalUrl]);

        } catch (\Exception $e) {
            Log::error("Error generating billing portal URL for team ID {$team->id}: " . $e->getMessage());
            // Specific check for common "No Stripe ID" error
            if (str_contains($e->getMessage(), 'No Stripe ID found')) {
                 return response()->json(['message' => 'This team is not yet a Stripe customer or has no subscription history. Please subscribe to a plan first.'], 422);
            }
            return response()->json(['message' => 'Error accessing billing portal: ' . $e->getMessage()], 500);
        }
    }
}
