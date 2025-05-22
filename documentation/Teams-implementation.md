# SermonAssist SaaS Platform - Teams Implementation Addendum

## 1. Executive Summary

This document serves as an addendum to the existing SermonAssist SaaS Platform Technical Design Document (TDD) to incorporate Laravel Jetstream's team functionality. This enhancement enables churches to have multiple users (pastors, assistants, content creators) under a single account, promoting collaboration while maintaining a simplified subscription model.

## 2. Updated System Architecture

### 2.1 Team-Based Structure

The SermonAssist platform will now use Jetstream's teams feature to represent churches/ministries:

- Each church/ministry operates as a "team" in the system
- The primary pastor or administrator creates the team and manages the subscription
- Additional team members (associate pastors, assistants) can be invited to collaborate
- Role-based permissions control access to sermons, templates, and features
- Resources (sermons, templates, news collections) are shared within the team

### 2.2 Updated Architecture Diagram

```
[Client Browser]
    |
    | HTTPS (TLS 1.3)
    |
[Laravel Application (Docker)]
    ├── Frontend (Inertia.js + Vue 3, TypeScript)
    ├── Backend (Laravel, TypeScript)
    ├── Authentication (Jetstream with Teams)
    ├── Team Management (Jetstream Teams API)
    ├── Subscription (Cashier, Team-Based Plans)
    ├── Queue (Redis)
    └── Cache (Redis)
    |
[PostgreSQL Database]
    └── Team data, user roles, sermons, templates, plans
[Sentry (Self-Hosted, Docker)]
    └── Error tracking
[External APIs]
    ├── Bible Indexing System (Verse retrieval)
    ├── News Monitoring System (Summaries)
    └── Stripe (Payments)
```

## 3. Technical Requirements Updates

### 3.1 Updated Functional Requirements

- **Team Management & Collaboration**:
  - Create church/ministry teams with a team owner (primary pastor) [NEW-REQ-TEAM-001]
  - Invite additional users to teams with role-based permissions [NEW-REQ-TEAM-002]
  - Share sermons, templates, and resources within the team [NEW-REQ-TEAM-003]
  - Team-wide access to news summaries and Bible verse tools [NEW-REQ-TEAM-004]
  - Team administration by designated team admins [NEW-REQ-TEAM-005]

- **Subscription Management Updates**:
  - Team-based subscription plans (instead of user-based) [UPDATE-REQ-SAAS-006]
  - Standard plan: $20/month for up to 2 users [UPDATE-REQ-SAAS-006a]
  - Premium plan: $30/month for up to 5 users (post-MVP) [UPDATE-REQ-SAAS-006b]
  - Team-wide 14-day trial [UPDATE-REQ-SAAS-007]

- **Sermon Management Updates**:
  - Team-based sermon ownership (vs. individual) [UPDATE-REQ-SAAS-020]
  - Collaborative sermon editing by team members [UPDATE-REQ-SAAS-026]
  - Sermon visibility controls within teams [NEW-REQ-TEAM-006]

- **Admin Billing & Account Management**:
  - View and manage all team subscriptions [NEW-REQ-ADMIN-001]
  - Process refunds and adjustments [NEW-REQ-ADMIN-002]
  - Apply complimentary credits (free weeks/months) to accounts [NEW-REQ-ADMIN-003]
  - Generate financial reports (revenue, churn, MRR) [NEW-REQ-ADMIN-004]
  - Track subscription metrics and analytics [NEW-REQ-ADMIN-005]
  - Manage payment disputes and failed payments [NEW-REQ-ADMIN-006]
  - Audit subscription history and changes [NEW-REQ-ADMIN-007]

### 3.2 Non-Functional Requirements Updates
  
- **Security**: Role-based access controls for team resources [UPDATE-REQ-SYS-006]
- **Performance**: Support up to 5 concurrent users per team [UPDATE-REQ-SYS-002]
- **Scalability**: Initial support for 100 teams (500 users) [UPDATE-REQ-SYS-003]

## 4. Updated Database Design

### 4.1 Schema Additions and Modifications

```sql
-- Existing tables from Jetstream
CREATE TABLE teams (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    personal_team BOOLEAN NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE team_user (
    id BIGSERIAL PRIMARY KEY,
    team_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    role VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(team_id, user_id)
);

CREATE TABLE team_invitations (
    id BIGSERIAL PRIMARY KEY,
    team_id BIGINT NOT NULL,
    email VARCHAR(255) NOT NULL,
    role VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(team_id, email)
);

-- Updated table structures for team ownership
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    current_team_id BIGINT NULL, -- New field for current team context
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
    max_users INTEGER DEFAULT 2, -- New field for team size limit
    trial_days INTEGER DEFAULT 14,
    features JSONB, -- E.g., { "sermons_per_month": 10 }
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE subscriptions (
    id BIGSERIAL PRIMARY KEY,
    team_id BIGINT REFERENCES teams(id), -- Changed from user_id to team_id
    plan_id BIGINT REFERENCES plans(id),
    stripe_id VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'trial') NOT NULL,
    trial_ends_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- New tables for billing administration
CREATE TABLE subscription_adjustments (
    id BIGSERIAL PRIMARY KEY,
    subscription_id BIGINT REFERENCES subscriptions(id),
    admin_id BIGINT REFERENCES users(id),
    type ENUM('refund', 'credit', 'comp', 'other') NOT NULL,
    amount DECIMAL(8,2) NOT NULL,
    reason TEXT,
    stripe_refund_id VARCHAR(255),
    duration_days INTEGER, -- For comp periods
    start_date TIMESTAMP,
    end_date TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE billing_events (
    id BIGSERIAL PRIMARY KEY,
    team_id BIGINT REFERENCES teams(id),
    subscription_id BIGINT REFERENCES subscriptions(id),
    event_type ENUM('subscription_created', 'subscription_updated', 'payment_succeeded', 
                    'payment_failed', 'refund_processed', 'credit_applied', 
                    'comp_applied', 'plan_changed') NOT NULL,
    amount DECIMAL(8,2),
    description TEXT,
    admin_id BIGINT REFERENCES users(id), -- If admin-initiated
    stripe_event_id VARCHAR(255),
    created_at TIMESTAMP
);

CREATE TABLE invoices (
    id BIGSERIAL PRIMARY KEY,
    team_id BIGINT REFERENCES teams(id),
    subscription_id BIGINT REFERENCES subscriptions(id),
    stripe_invoice_id VARCHAR(255) NOT NULL,
    amount DECIMAL(8,2) NOT NULL,
    tax DECIMAL(8,2),
    total DECIMAL(8,2) NOT NULL,
    status ENUM('draft', 'open', 'paid', 'uncollectible', 'void') NOT NULL,
    billing_reason VARCHAR(255),
    invoice_pdf VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE sermons (
    id BIGSERIAL PRIMARY KEY,
    team_id BIGINT REFERENCES teams(id), -- Changed from user_id to team_id
    user_id BIGINT REFERENCES users(id), -- Creator of the sermon
    title VARCHAR(255) NOT NULL,
    content JSONB NOT NULL,
    template_id BIGINT,
    draft BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE templates (
    id BIGSERIAL PRIMARY KEY,
    team_id BIGINT REFERENCES teams(id), -- Changed from user_id to team_id
    user_id BIGINT REFERENCES users(id), -- Creator of the template
    name VARCHAR(255) NOT NULL,
    structure JSONB NOT NULL, -- E.g., { "sections": ["intro", "points", "conclusion"] }
    is_system BOOLEAN DEFAULT FALSE, -- For system templates
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 5. Laravel Package Updates

The following packages will be modified or added to support team functionality:

1. **laravel/jetstream** (with teams enabled):
   - Enable the teams feature during installation
   ```bash
   php artisan jetstream:install inertia --teams
   ```
   - Requirements: NEW-REQ-TEAM-001 to NEW-REQ-TEAM-005

2. **Modified laravel/cashier-stripe integration**:
   - Updated to support team-based billing
   - Requirements: UPDATE-REQ-SAAS-006, UPDATE-REQ-SAAS-007

3. **spatie/laravel-permission** (with team-specific roles):
   - Configure to support team-contextual roles
   - Requirements: UPDATE-REQ-SYS-006, NEW-REQ-TEAM-006
   
4. **laravel/cashier-stripe customizations**:
   - Extend Billable trait to work with teams
   - Create admin interfaces for managing subscriptions
   - Requirements: NEW-REQ-ADMIN-001 to NEW-REQ-ADMIN-007

5. **maatwebsite/laravel-excel**:
   - Support for exporting billing reports
   - Generate financial summaries and subscription analytics
   - Requirements: NEW-REQ-ADMIN-004, NEW-REQ-ADMIN-005

6. **spatie/laravel-activitylog**:
   - Track billing adjustments and admin actions
   - Maintain audit trail for compliance
   - Requirements: NEW-REQ-ADMIN-007

## 6. Implementation Plan Updates

### 6.1 Sprint Breakdown Modifications

- **Sprint 1 (Updated)**:
  - Set up Laravel 11.x with Jetstream **teams** enabled (Inertia.js, Vue 3, TypeScript)
  - Implement team registration with email verification
  - Configure team roles and permissions
  - User stories: REQ-SAAS-001, REQ-SAAS-002, NEW-REQ-TEAM-001, NEW-REQ-TEAM-002

- **Sprint 2 (Updated)**:
  - Adapt Stripe integration for team-based billing
  - Set up team-based subscription plans with user limits
  - Create team management UI for invitations and role management
  - User stories: UPDATE-REQ-SAAS-006, UPDATE-REQ-SAAS-007, NEW-REQ-TEAM-005

- **Sprint 3 (Updated)**:
  - Build team dashboard with shared sermon history, news summaries
  - Configure team-based content visibility
  - User stories: UPDATE-REQ-SAAS-014, REQ-SAAS-017, NEW-REQ-TEAM-003, NEW-REQ-TEAM-004

- **Sprint 4 (Updated)**:
  - Implement sermon workspace with team-based sharing and visibility
  - Add collaborative indicators (who's editing what)
  - User stories: UPDATE-REQ-SAAS-020, UPDATE-REQ-SAAS-026, NEW-REQ-TEAM-006

- **Sprint 5 (New)**:
  - Develop admin billing management interface
  - Implement refund and adjustment processing
  - Create subscription audit trail
  - User stories: NEW-REQ-ADMIN-001, NEW-REQ-ADMIN-002, NEW-REQ-ADMIN-003, NEW-REQ-ADMIN-007

- **Sprint 6 (New)**:
  - Build financial reporting and analytics dashboard
  - Implement export functionality for financial data
  - Create billing events monitoring system
  - User stories: NEW-REQ-ADMIN-004, NEW-REQ-ADMIN-005, NEW-REQ-ADMIN-006

## 7. Team-Specific User Interface Updates

### 7.1 Team Management Interface

- Team creation during registration
- Team settings page for:
  - Inviting new members
  - Managing member roles
  - Subscription management
  - Team profile settings

### 7.2 Team-Aware Dashboard

- Switch between teams (if user belongs to multiple)
- Team-specific sermon listing
- Team activity indicators
- Role-based feature availability

### 7.3 Collaborative Features

- User attribution for sermon creation/editing
- Indicators for sermons currently being edited
- Team-wide sermon templates
- Team-wide saved news items

### 7.4 Admin Billing & Account Management Interface

- **Subscription Overview Dashboard**:
  - Active subscriptions count and revenue metrics
  - MRR (Monthly Recurring Revenue) and growth charts
  - Churn rate and retention analytics
  - Trial conversion metrics

- **Team Account Management**:
  - Search and filter teams by plan, status, creation date
  - View subscription details, payment history, and plan information
  - Manage team billing contacts and payment methods
  - Notes and support history tracking

- **Billing Operations**:
  - Process refunds (full or partial)
  - Apply account credits or complementary periods
  - Extend trial periods
  - Handle payment disputes and failed payments
  - Override subscription status (active/inactive)

- **Financial Reporting**:
  - Revenue reports (daily, monthly, yearly)
  - Subscription plan distribution
  - Team growth and churn metrics
  - Export data to CSV/Excel formats
  - Tax and financial compliance reporting

- **Audit Trail**:
  - Activity log for all billing operations
  - Admin action history with timestamps and user identification
  - Changes to subscription plans and adjustments
  - System-wide billing event monitoring

## 8. Security and Permission Updates

### 8.1 Team-Based Roles

- **Team Owner**: Full access to all team features, billing management
- **Team Admin**: Can manage team members, create/edit all sermons
- **Team Member**: Basic access to create/edit own sermons, view team resources

### 8.2 Resource Permissions

- Object-level permissions for sermons and templates
- Visibility controls (team-wide vs. creator-only)
- Role-based feature access

## 9. API Updates

Updated API endpoints to support team context:

- **Endpoints** (Jetstream Authentication with team context):
  - `POST /api/teams/{team}/sermons`: Create/save sermon draft
  - `GET /api/teams/{team}/sermons`: List team sermons
  - `GET /api/teams/{team}/templates`: List team templates
  - `GET /api/teams/{team}/members`: List team members
  - `POST /api/teams/{team}/invitations`: Invite new team member

- **Admin Billing API Endpoints**:
  - `GET /api/admin/teams`: List all teams with filtering options
  - `GET /api/admin/teams/{team}/subscription`: Get team subscription details
  - `GET /api/admin/teams/{team}/invoices`: Get team invoice history
  - `GET /api/admin/teams/{team}/events`: Get team billing events
  - `POST /api/admin/teams/{team}/refund`: Process refund
  - `POST /api/admin/teams/{team}/credit`: Apply account credit
  - `POST /api/admin/teams/{team}/comp`: Apply complimentary period
  - `PUT /api/admin/teams/{team}/subscription`: Update subscription status
  - `GET /api/admin/reports/revenue`: Generate revenue reports
  - `GET /api/admin/reports/subscriptions`: Generate subscription reports
  - `GET /api/admin/reports/exports/{type}`: Export financial data

## 10. Glossary

- **Team**: Represents a church or ministry organization in the system
- **Team Owner**: Primary account creator (typically senior pastor or administrator)
- **Team Admin**: User with elevated permissions for team management
- **Team Member**: Basic user with access to team resources
- **Jetstream Teams**: Laravel Jetstream's built-in multi-tenancy feature