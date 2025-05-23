# Child Task List for T8: Set up database schema and migrations

- [ ] **Define User Table Migration:**
    - [ ] Review existing `0001_01_01_000000_create_users_table.php` migration.
    - [ ] Identify any missing fields based on `saas-requirements.md` and `SaaS-TDD.md`.
    - [ ] Create a new migration file if modifications are needed, ensuring no data loss for existing users.
    - [ ] Add appropriate indexes for performance.

- [ ] **Create Sermon Table Migration with pgvector:**
    - [ ] Define schema for the `sermons` table referencing `saas-requirements.md`.
    - [ ] Include fields for title, body (text), audio_path (string), video_path (string), date (date/timestamp), speaker (string), series (string, nullable).
    - [ ] Add a `vector` column for `pgvector` embeddings as per `ai-integration-plan.md`.
    - [ ] Ensure the migration enables the `pgvector` extension in PostgreSQL.
    - [ ] Add indexes for searchable fields (title, speaker, series, date).
    - [ ] Ensure compliance with PSR-12 standards.

- [ ] **Create Teams Table Migration:**
    - [ ] Review existing `2025_05_19_020545_create_teams_table.php` migration.
    - [ ] Identify any missing fields based on `saas-requirements.md` (e.g., team owner, subscription details if not in a separate table).
    - [ ] Create a new migration file if modifications are needed.
    - [ ] Add indexes for performance (e.g., owner_id).

- [ ] **Create Subscriptions Table Migration:**
    - [ ] Define schema for a `subscriptions` table.
    - [ ] Include fields for `team_id` (foreign key to teams), `plan_id` (foreign key to plans), `start_date`, `end_date`, `status` (e.g., active, cancelled, past_due).
    - [ ] Reference `saas-requirements.md` for subscription model details.
    - [ ] Consult with BE2 regarding any specific fields needed for billing integration or AI feature tiers. (Manual step, assume completed for now).
    - [ ] Add indexes for `team_id`, `plan_id`, and `status`.
    - [ ] Ensure compliance with PSR-12 standards.

- [ ] **Review and Finalize All Migrations:**
    - [ ] Ensure all foreign key constraints are correctly defined.
    - [ ] Verify PSR-12 compliance across all new/modified migration files.
    - [ ] Confirm all necessary indexes are in place for performance as per `documents/developmentGuidelines`.
    - [ ] Test migrations locally (if possible, or prepare for testing in a later step).
```
