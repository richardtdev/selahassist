<?php

namespace App\Listeners;

use Laravel\Jetstream\Events\TeamCreated; // Corrected namespace for TeamCreated
use App\Models\Plan;
use Illuminate\Support\Facades\Log;
// use Illuminate\Contracts\Queue\ShouldQueue; // Uncomment if you want it to be queueable
// use Illuminate\Queue\InteractsWithQueue; // Uncomment if you want it to be queueable

class StartTeamTrialListener // Potentially implements ShouldQueue
{
    // use InteractsWithQueue; // Uncomment if you want it to be queueable

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TeamCreated $event): void
    {
        $team = $event->team;
        Log::info("StartTeamTrialListener triggered for team ID: {$team->id}, Name: {$team->name}");

        $premiumPlan = Plan::where('slug', 'premium')->first();

        if ($premiumPlan && !empty($premiumPlan->stripe_plan_id) && $premiumPlan->trial_days > 0) {
            try {
                Log::info("Attempting to start trial for team ID: {$team->id} on plan '{$premiumPlan->name}' (Stripe ID: {$premiumPlan->stripe_plan_id}) for {$premiumPlan->trial_days} days.");
                // Ensure the Team model uses the Billable trait
                if (method_exists($team, 'newSubscription')) {
                    $team->newSubscription('default', $premiumPlan->stripe_plan_id)
                         ->trialDays($premiumPlan->trial_days)
                         ->add(); // Consider addStripeCustomer() if customer doesn't exist, though newSubscription often handles this.
                    Log::info("Trial subscription created successfully for team ID: {$team->id} to plan '{$premiumPlan->name}'.");
                } else {
                    Log::error("Team model (ID: {$team->id}) does not use the Billable trait or newSubscription method is unavailable.");
                }
            } catch (\Exception $e) {
                Log::error("Failed to create trial subscription for team ID: {$team->id}. Error: " . $e->getMessage());
            }
        } else {
            $logMessage = "Premium plan (slug: 'premium') not found, or stripe_plan_id is missing, or trial_days is not greater than 0.";
            if ($premiumPlan) {
                $logMessage .= " Found Plan: Name='{$premiumPlan->name}', StripeID='{$premiumPlan->stripe_plan_id}', TrialDays='{$premiumPlan->trial_days}'.";
            }
            Log::warning($logMessage);
        }
    }
}
