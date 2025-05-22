# SermonAssist SaaS Platform Technical Design Document

## 1. Introduction
The SermonAssist SaaS Platform is a Laravel-based web application enabling pastors to create and manage sermons with integrated Bible verse indexing, news summaries, and sermon templates. Designed as a standalone product, it uses Inertia.js with Vue 3 and TypeScript for a server-driven UI. This document outlines the technical design for the Minimum Viable Product (MVP), incorporating tiered subscription plan management, ensuring modularity, accessibility, and alignment with SelahVerse’s mission. Hosting and deployment details will be covered in a separate TDD.

### 1.1 Purpose
To define the architecture, components, Laravel packages, and implementation strategy for the SermonAssist SaaS Platform MVP, supporting Agile development, tiered plan management, and system-wide requirements.

### 1.2 Scope
- **MVP Features**:
  - User registration with email verification.
  - Secure login with 2FA.
  - Role-based access (admin, pastor).
  - Tiered subscription management ($20/month standard plan with 14-day trial, admin interface to manage plans).
  - Pastor dashboard with sermon history and news summaries.
  - Sermon workspace with rich text editor, Bible verse insertion (via Bible Indexing System), sermon templates, and auto-save.
  - Daily news summaries with scripture connections (via News Monitoring System).
  - Security (encryption, GDPR/CCPA compliance).
- **Non-MVP Features** (Deferred): Premium $30/month plan, RAG-based chatbot, social login, full billing portal, collaborative editing, analytics, export options.
- **System-Wide**: 99.9% uptime, <3-second response times, 500 concurrent users, WCAG 2.1 AA, GDPR/CCPA compliance, Cypress testing, self-hosted Sentry.
- **Hosting**: Deferred to a separate Hosting and Deployment TDD, covering VPS, Docker, and infrastructure for all products.

### 1.3 Assumptions
- Laravel 11.x and Jetstream support Inertia.js, TypeScript, and required packages.
- Bible Indexing System provides a RESTful API (e.g., `/bible/verses`).
- News Monitoring System provides a RESTful API for summaries.
- VPS environment supports Docker, PostgreSQL, Redis, and Sentry.
- Stripe supports dynamic plan creation for tiered subscriptions.

### 1.4 Constraints
- Budget: $30,000-$40,000 (subset of $167,600-$234,000).
- Timeline: 3-5 months for MVP (Q3 2025).
- Theological neutrality across denominations.
- Initial support for 500 users.

## 2. System Architecture
The SaaS platform is a Laravel application using Inertia.js for frontend rendering, with TypeScript for type safety and self-hosted Sentry for error tracking. It integrates with external APIs and runs in a Dockerized VPS environment.

### 2.1 Architecture Diagram
```
[Client Browser]
    |
    | HTTPS (TLS 1.3)
    |
[Laravel Application (Docker)]
    ├── Frontend (Inertia.js + Vue 3, TypeScript)
    ├── Backend (Laravel, TypeScript)
    ├── Authentication (Jetstream)
    ├── Subscription (Cashier, Plan Management)
    ├── Queue (Redis)
    └── Cache (Redis)
    |
[PostgreSQL Database]
    └── User data, sermons, templates, plans
[Sentry (Self-Hosted, Docker)]
    └── Error tracking
[External APIs]
    ├── Bible Indexing System (Verse retrieval)
    ├── News Monitoring System (Summaries)
    └── Stripe (Payments)
```

### 2.2 Components
- **Frontend**:
  - Inertia.js with Vue 3 (TypeScript) for server-driven UI.
  - Tailwind CSS for WCAG 2.1 AA-compliant, responsive design.
  - Handles dashboard, sermon workspace, templates, and subscription plan selection.
- **Backend**:
  - Laravel 11.x with TypeScript (via `ts-node` or transpilation).
  - Jetstream for authentication, registration, 2FA.
  - Cashier for Stripe subscriptions and tiered plan management.
  - Redis for caching and queues (e.g., news fetching, emails).
- **Database**:
  - PostgreSQL for user profiles, sermons, templates, and subscription plans.
  - Encrypted at rest.
- **Error Tracking**:
  - Self-hosted Sentry for frontend/backend errors.
- **External Integrations**:
  - Bible Indexing System API for verse retrieval.
  - News Monitoring System API for summaries.
  - Stripe API for payments and plan management.
- **Infrastructure**:
  - Dockerized Laravel app with PostgreSQL and Redis.
  - Detailed hosting/deployment in separate TDD.

### 2.3 Modularity
- **Standalone Operation**: Functions without News integration (relies on Bible Indexing and templates).
- **External Integration**: Minimal RESTful API (e.g., `/api/sermons`, `/api/plans`) for future integrations.
- **Dockerized Deployment**: Portable across VPS environments.

## 3. Laravel Packages
The following packages support the MVP, covering authentication, subscriptions, plan management, frontend, integrations, and system-wide requirements:
1. **laravel/jetstream**:
   - Authentication, Inertia.js (Vue 3), Tailwind CSS, 2FA.
   - Requirements: REQ-SAAS-001, REQ-SAAS-002, REQ-SAAS-003 (partial).
2. **laravel/cashier-stripe**:
   - Stripe subscriptions and tiered plan management ($20/month, 14-day trial).
   - Requirements: REQ-SAAS-006 to REQ-SAAS-008.
3. **inertiajs/inertia-laravel**:
   - Server-driven rendering with Vue 3 (TypeScript).
   - Requirements: REQ-SAAS-014, REQ-SAAS-017, REQ-SAAS-020 to REQ-SAAS-024, REQ-SAAS-033, REQ-SAAS-034.
4. **laravel/sanctum**:
   - Token-based API authentication (minimal API).
   - Requirements: REQ-SYS-009, REQ-SAAS-046.
5. **spatie/laravel-permission**:
   - Role-based access for admin/pastor, including plan management.
   - Requirements: REQ-SAAS-003.
6. **laravel/horizon**:
   - Redis queue management for async tasks.
   - Requirements: REQ-SYS-002, REQ-SAAS-033.
7. **laravel/telescope** (dev only):
   - Debug requests, queries during development.
   - Requirements: REQ-SYS-020.
8. **barryvdh/laravel-debugbar** (dev only):
   - Profile queries, performance.
   - Requirements: REQ-SYS-002.
9. **spatie/laravel-backup**:
   - Automated database backups.
   - Requirements: REQ-SYS-019, REQ-SAAS-047.
10. **guzzlehttp/guzzle**:
    - HTTP client for Bible/News APIs.
    - Requirements: REQ-SAAS-022, REQ-SAAS-033, REQ-SAAS-034.
11. **tightenco/ziggy**:
    - JavaScript routes for Inertia.js.
    - Requirements: REQ-SAAS-014, REQ-SAAS-020.
12. **spatie/laravel-translatable** (optional):
    - Multi-language UI support (future-proofing).
    - Requirements: REQ-SYS-028.
13. **spatie/laravel-ignition**:
    - Error reporting, Sentry integration.
    - Requirements: REQ-SYS-020.
14. **laravel/pint**:
    - Code style enforcement.
    - Requirements: N/A (quality).
15. **nunomaduro/larastan**:
    - Static analysis for PHP/TypeScript.
    - Requirements: N/A (quality).

## 4. Technical Requirements
### 4.1 Functional Requirements (MVP)
- **User Management & Authentication**:
  - Registration with email verification (Jetstream) [REQ-SAAS-001].
  - Login with 2FA (Google Authenticator) [REQ-SAAS-002].
  - Roles: Admin (manage users, plans), Pastor (create sermons) [REQ-SAAS-003].
- **Subscription & Plan Management**:
  - Tiered subscriptions ($20/month standard plan, 14-day trial) with admin interface to manage plans (create/update) [REQ-SAAS-006, extended].
  - Secure payment processing via Stripe [REQ-SAAS-008].
  - User interface to view and subscribe to plans (MVP: standard plan only) [REQ-SAAS-007].
- **Pastor Dashboard**:
  - Show sermon history, news summaries (Inertia.js, Vue 3) [REQ-SAAS-014, REQ-SAAS-017].
- **Sermon Creation Workspace**:
  - Rich text editor (Tiptap.js, TypeScript) with formatting [REQ-SAAS-020, REQ-SAAS-021].
  - Bible verse insertion via Bible Indexing API [REQ-SAAS-022].
  - Sermon templates (system-defined, user-saved) [REQ-SAAS-024].
  - Auto-save drafts to PostgreSQL (every 10 seconds) [REQ-SAAS-023].
- **Current Events**:
  - Display news summaries with scripture links (News Monitoring API) [REQ-SAAS-033, REQ-SAAS-034].

### 4.2 Non-Functional Requirements
- **Performance**: 99.9% uptime, <3-second page loads, 500 users [REQ-SYS-001 to REQ-SYS-003].
- **Security**: AES-256 encryption, OWASP compliance, Jetstream tokens [REQ-SYS-006, REQ-SYS-008, REQ-SAAS-046].
- **Compliance**: GDPR/CCPA with data export/deletion [REQ-SYS-021, REQ-SAAS-047].
- **Accessibility**: WCAG 2.1 AA, tested with axe-core [REQ-SYS-027].
- **Scalability**: Supports 500 users, with scaling in Hosting TDD.
- **Testing**: Cypress for end-to-end, PHPUnit for unit tests.

## 5. Database Design
### 5.1 Schema
```sql
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pastor') NOT NULL DEFAULT 'pastor',
    two_factor_secret TEXT,
    email_verified_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE plans (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL, -- E.g., 'standard'
    stripe_plan_id VARCHAR(255) NOT NULL,
    price DECIMAL(8,2) NOT NULL, -- E.g., 20.00
    trial_days INTEGER DEFAULT 14,
    features JSONB, -- E.g., { "sermons_per_month": 10 }
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE subscriptions (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),
    plan_id BIGINT REFERENCES plans(id),
    stripe_id VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'trial') NOT NULL,
    trial_ends_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE sermons (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),
    title VARCHAR(255) NOT NULL,
    content JSONB NOT NULL,
    template_id BIGINT,
    draft BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE templates (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id), -- NULL for system templates
    name VARCHAR(255) NOT NULL,
    structure JSONB NOT NULL, -- E.g., { "sections": ["intro", "points", "conclusion"] }
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity INTEGER
);
```

### 5.2 Encryption
- Passwords hashed with bcrypt.
- Content/templates/features (JSONB) encrypted with Laravel’s `encrypt` helper.
- Database encryption at rest (configured in Hosting TDD).

## 6. API Design (Minimal for MVP)
Inertia.js reduces API needs, but a minimal RESTful API supports plan management and future integrations.
- **Endpoints** (Jetstream Authentication):
  - `POST /api/sermons`: Create/save sermon draft [REQ-SAAS-023].
  - `GET /api/sermons`: List user’s sermons [REQ-SAAS-014].
  - `GET /api/templates`: List templates [REQ-SAAS-024].
  - `GET /api/plans`: List active plans (for subscription UI).
  - `POST /api/plans` (admin): Create/update plan.
- **Example Request**:
```http
GET /api/plans HTTP/1.1
Authorization: Bearer {token}

HTTP/1.1 200 OK
Content-Type: application/json
{
    "plans": [
        {"id": 1, "name": "standard", "price": 20.00, "trial_days": 14, "features": {"sermons_per_month": 10}}
    ]
}
```

## 7. Integration with Other Products
- **Bible Indexing System**:
  - Call `/bible/verses` for verse retrieval in workspace.
  - Cache in Redis for <3-second latency.
- **News Monitoring System**:
  - Call `/news/summaries` for dashboard display.
  - Async queue (Redis, Horizon) for fetching.
- **RAG System** (Post-MVP):
  - Future integration for chatbot functionality [REQ-SAAS-028, REQ-SAAS-029].

## 8. Implementation Plan (Agile)
### 8.1 Sprint Breakdown (2-Week Sprints, Months 3-5)
- **Sprint 1**:
  - Set up Laravel 11.x, Jetstream (Inertia.js, Vue 3, TypeScript), Cashier, Horizon, Sentry integration.
  - Implement registration/login with email verification.
  - Start Cypress testing, WCAG compliance (axe-core).
  - Packages: jetstream, inertia-laravel, pint, larastan, ignition.
  - User stories: REQ-SAAS-001, REQ-SAAS-002 (partial).
- **Sprint 2**:
  - Add 2FA, role-based access (spatie/laravel-permission).
  - Set up Stripe with $20/month standard plan, 14-day trial, and admin plan management UI.
  - Packages: cashier-stripe, laravel-permission.
  - User stories: REQ-SAAS-002, REQ-SAAS-003, REQ-SAAS-006 to REQ-SAAS-008.
- **Sprint 3**:
  - Build dashboard with sermon history, news summaries (Inertia.js, ziggy).
  - Integrate News Monitoring API (guzzle).
  - Packages: guzzlehttp/guzzle, tightenco/ziggy.
  - User stories: REQ-SAAS-014, REQ-SAAS-017, REQ-SAAS-033, REQ-SAAS-034.
- **Sprint 4**:
  - Implement sermon workspace with Tiptap.js, Bible Indexing API, auto-save, templates.
  - Packages: guzzlehttp/guzzle.
  - User stories: REQ-SAAS-020 to REQ-SAAS-024.
- **Sprint 5**:
  - Finalize security (encryption, GDPR), accessibility, and plan management UI.
  - Packages: spatie/laravel-backup.
  - User stories: REQ-SAAS-046, REQ-SAAS-047, plan management.

### 8.2 Backlog (Post-MVP)
- Premium $30/month plan, full billing portal [REQ-SAAS-009, REQ-SAAS-011].
- RAG-based chatbot [REQ-SAAS-028, REQ-SAAS-029].
- Social login, collaborative editing, analytics [REQ-SAAS-004, REQ-SAAS-026, REQ-SAAS-042].

## 9. Performance and Scalability
- **Caching**: Redis for news summaries, templates, plans (Horizon).
- **Database**: PostgreSQL with indexes on `users.email`, `sermons.user_id`, `templates.user_id`, `plans.id`.
- **Scaling**: Supports 500 users; details in Hosting TDD.
- **Monitoring**: Sentry for errors, metrics in Hosting TDD.

## 10. Security and Compliance
- **Authentication**: Jetstream tokens, 2FA.
- **Encryption**: TLS 1.3, AES-256 for data at rest (via Hosting TDD).
- **Compliance**: GDPR/CCPA with export (`/api/user/export`), deletion.
- **Audits**: Monthly OWASP scans, Sentry logs.
- **Backups**: Configured in Hosting TDD (spatie/laravel-backup).

## 11. Testing Strategy
- **Unit Tests**: PHPUnit for controllers, models (including plan management).
- **End-to-End Tests**: Cypress for registration, sermon creation, template use, plan subscription.
- **Accessibility Tests**: axe-core, Lighthouse for WCAG 2.1 AA.
- **Integration Tests**: Test Bible/News API integrations, Stripe plan management (guzzle, cashier).
- **Performance Tests**: Validate <3-second latency, 500 users.
- **Security Tests**: OWASP ZAP scans, GDPR checks.

## 12. Deployment
- **CI/CD**: GitHub Actions for testing, deployment (details in Hosting TDD).
- **Containerization**: Docker for Laravel app.
- **Environment**: Staging (beta), Production (launch).

## 13. Risks and Mitigations
- **Risk**: Bible Indexing API downtime (Medium likelihood, High impact).
  - **Mitigation**: Cache verses in Redis, fallback to static data.
- **Risk**: Accessibility issues (Medium likelihood, Medium impact).
  - **Mitigation**: axe-core testing from Sprint 1, pastor feedback.
- **Risk**: Plan management complexity (Medium likelihood, Medium impact).
  - **Mitigation**: Simplify admin UI, beta test with admins.

## 14. Future Considerations
- Add premium plan and RAG-based chatbot.
- Scale to 5,000+ users (Hosting TDD).
- Support mobile app (Year 2).

## 15. Glossary
- **Jetstream**: Laravel starter kit for authentication, Inertia.js.
- **Inertia.js**: Server-driven rendering with Vue 3.
- **Tiptap.js**: Rich text editor for sermons.
- **Sentry**: Open-source error tracking.