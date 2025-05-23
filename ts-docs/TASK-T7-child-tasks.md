# Child Task List for T7: Implement Subscription Management

| Child Task ID | Subtask Description                                                                                                                               | Status      |
|---------------|---------------------------------------------------------------------------------------------------------------------------------------------------|-------------|
| T7.1          | Install Laravel Cashier (Stripe). Configure Stripe API keys, webhook secret, and currency (USD).                                                    | Not Started |
| T7.2          | Adapt `Team` model to use Cashier's `Billable` trait. Ensure subscriptions are linked to `teams` table.                                             | Not Started |
| T7.3          | Create `Plan` model and migration for `plans` table (name, stripe_plan_id, price, trial_days, features JSONB (e.g., max_users), active). Define Standard Plan ($20/month, 2 users) and structure for Premium Plan ($30/month, 5 users). | Not Started |
| T7.4          | Implement logic for 14-day team-wide trial for "premium features" upon team creation.                                                              | Not Started |
| T7.5          | Implement `GET /api/plans` endpoint to list active subscription plans (name, price, features).                                                      | Not Started |
| T7.6          | Implement mechanism for a team to subscribe to the Standard Plan (e.g., using Stripe Checkout session generation via Cashier).                     | Not Started |
| T7.7          | Implement mechanism for a team owner/admin to cancel their team's active subscription (via Cashier methods).                                          | Not Started |
| T7.8          | Implement mechanism for a team owner/admin to update payment method (e.g., via Cashier's customer portal redirect or Stripe Elements).             | Not Started |
| T7.9          | (Post-MVP Refinement) Implement plan change functionality (upgrade/downgrade) with proration.                                                       | Not Started |
| T7.10         | Configure and handle essential Stripe webhooks (e.g., `customer.subscription.created`, `customer.subscription.updated`, `customer.subscription.deleted`, `invoice.payment_succeeded`, `invoice.payment_failed`). Ensure `subscriptions` table status is updated. | Not Started |
| T7.11         | Configure 7-day grace period for failed payments within Stripe/Cashier dunning settings.                                                            | Not Started |
| T7.12         | Implement logic to expose a team's current subscription status, plan, and basic billing history (e.g., next payment date) for team portal.        | Not Started |
| T7.13         | Implement GDPR/CCPA compliance for subscription data: data access (view subscription details), portability (export), and deletion (anonymize/delete Stripe customer & subscription data upon request). Consider using `Laravel GDPR` package. | Not Started |
| T7.14         | Write feature tests for team subscription lifecycle: subscribing to Standard Plan, trial activation, cancellation, payment method update, webhook handling. | Not Started |
| T7.15         | Write unit tests for any custom logic in Plan model, subscription services, or webhook handlers.                                                   | Not Started |
| T7.16         | Ensure all new PHP code adheres to PSR-12 standards, uses PHP 8.1+ strict typing, and includes PHPDoc comments.                                    | Not Started |
| T7.17         | Consult with BE1 (Jetstream teams expert) for `Team` model integration and ensuring subscription status correctly interacts with team features/limits. | Not Started |
