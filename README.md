# SermonAssist SaaS Platform

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 1. What is the App

### Overview
SermonAssist is an AI-powered SaaS platform under the SelahVerse brand, designed to empower pastors by streamlining sermon preparation, ensuring theological accuracy, and integrating current events. It enables the creation of biblically sound, culturally relevant sermons, reducing preparation time by 30-50%.

### Key Features
- **User Authentication**: Jetstream with team-based collaboration.
- **Subscription Management**: Tiered plans via Laravel Cashier (Stripe).
- **Pastor Dashboard**: Sermon history and AI-generated news summaries.
- **Sermon Creation**: Rich text editor (Tiptap.js) with Bible verse integration.
- **News Integration**: Daily summaries connecting current events to scripture.
- **Compliance**: GDPR/CCPA, WCAG 2.1 AA accessibility, self-hosted Sentry for error tracking.

### Learn More
For a detailed overview, see the [Product Requirements Document (PRD)](documents/PlanningDocs/saas-prd.md) and [Technical Design Document (TDD)](documents/PlanningDocs/SaaS-TDD.md) in `documents/PlanningDocs`.

## 2. Technical Stack

### Backend
- **Framework**: Laravel 11.x
- **Database**: PostgreSQL with `pgvector` extension for AI features
- **Authentication**: Laravel Jetstream with teams
- **Payments**: Laravel Cashier (Stripe)
- **Caching**: Redis
- **Error Tracking**: Self-hosted Sentry

### Frontend
- **Framework**: Inertia.js, Vue 3
- **Language**: TypeScript
- **Editor**: Tiptap.js for rich text sermon creation
- **Styling**: Tailwind CSS

### AI Integrations
- **External Systems**: Bible Indexing System, News Monitoring System, Retrieval-Augmented Generation (RAG) System
- **Purpose**: Verse retrieval, news summaries, sermon outline generation

### Development Tools
- **Containerization**: Laravel Sail (Docker)
- **Debugging**: Laravel Telescope, Debugbar (development only)
- **Version Control**: Git

See [laravel-packages.md](documentation/laravel-packages.md) in `documentation` for a full list of packages.

## 3. Requirements to Run

### System Requirements
- **OS**: Linux, macOS, or Windows (WSL2 recommended for Windows)
- **Memory**: Minimum 4GB RAM, 8GB recommended
- **Storage**: 2GB free disk space for dependencies and database

### Software Requirements
- **PHP**: 8.2 or higher
- **Composer**: 2.x
- **Node.js**: 18.x or higher
- **npm**: 8.x or higher
- **PostgreSQL**: 13.x or higher with `pgvector` extension
- **Redis**: 6.x or higher
- **Git**: 2.x or higher

### Optional Tools
- **Docker**: For Laravel Sail
- **Stripe CLI**: For testing payment webhooks
- **Sentry CLI**: For error tracking setup

## 4. How to Get Setup

### Clone the Repository
```bash
git clone <repository-url>
cd sermonassist
```

### Install Dependencies
```bash
composer install
npm install
```

### Configure Environment
1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
2. Update `.env` with your settings (database, Stripe, API keys).
3. Generate an application key:
   ```bash
   php artisan key:generate
   ```

### Set Up Database
1. Configure PostgreSQL in `.env` (ensure `pgvector` is enabled).
2. Run migrations:
   ```bash
   php artisan migrate
   ```

### Install Jetstream with Teams
```bash
php artisan jetstream:install inertia --teams
npm install && npm run build
```

### Run the Application
1. Start the Laravel server:
   ```bash
   php artisan serve
   ```
2. Compile frontend assets:
   ```bash
   npm run dev
   ```

### Optional Development Setup
- **Laravel Sail** (Docker):
  ```bash
  php artisan sail:install
  ./vendor/bin/sail up
  ```
- **Debugging Tools**:
  ```bash
  composer require laravel/telescope
  php artisan telescope:install
  ```

For details, refer to the [Technical Design Document](documents/PlanningDocs/SaaS-TDD.md) and [AI Integration Plan](documents/PlanningDocs/ai-integration-plan.md).

## 5. Development Guidelines

### Coding Standards
- **PHP**: Follow PSR-12 standards (see `documents/developmentGuidelines/AI Development Guidelines for Cursor Rules`).
- **Vue/TypeScript**: Adhere to Vue 3 composition API and TypeScript best practices.
- **Testing**: Write unit and feature tests using PHPUnit and Vue Testing Library.

### Regulations
- **Data Privacy**: Comply with GDPR and CCPA for user data handling.
- **Licensing**: Use MIT-licensed dependencies where possible.

### Accessibility
- **Standard**: WCAG 2.1 AA compliance for all frontend components.
- **Tools**: Use axe-core for accessibility testing.
- **Guidelines**: See `documents/developmentGuidelines/AI Development Guidelines for Additional Cursor Rules` for accessibility rules.

### Security Protocols
- **Authentication**: Use Laravel Sanctum for API tokens, Jetstream for user sessions.
- **Data Protection**: Encrypt sensitive data (e.g., payment info) using Laravel’s encryption.
- **Error Tracking**: Configure self-hosted Sentry for production.
- **Vulnerability Reporting**: Email [security@sermonassist.com](mailto:security@sermonassist.com) for vulnerabilities.

### Workflow
- **Git**: Follow branching and commit message guidelines in `documents/developmentGuidelines`.
- **CI/CD**: Use GitHub Actions for automated testing and deployment.
- **Documentation**: Keep `README.md` and related docs updated per `documentation.mdc`.

## 6. AI Use Policy

### Policy Statement
AI use is permitted and encouraged for development, testing, and documentation tasks in the SermonAssist project. AI agents can leverage project documentation to generate code, troubleshoot issues, and create content, provided they adhere to the development guidelines and security protocols outlined above.

### Scope
- **Allowed Tasks**: Code generation, debugging, documentation updates, UI design, and testing.
- **Restrictions**: AI must not modify production data or access sensitive credentials without human oversight.

## 7. AI Documentation

### Overview
This section details documentation for AI agents to interact with the SermonAssist project effectively. Documentation is stored in the `documents` and `documentation` directories, providing context for project requirements, architecture, and development standards.

### Documentation Details

#### Development Guidelines
- **Location**: `documents/developmentGuidelines`
- **Purpose**: Defines coding standards, testing strategies, and workflows (e.g., PSR-12, WCAG 2.1 AA, Vue testing).
- **Key Files**:
  - `AI Development Guidelines for Cursor Rules`: Core standards for Laravel and Vue.
  - `AI Development Guidelines for Additional Cursor Rules`: Rules for documentation, Git, security, and accessibility.

#### Planning Documentation
- **Location**: `documents/PlanningDocs`
- **Purpose**: Outlines project vision, requirements, architecture, and AI integration.
- **Key Files**:
  - `saas-prd.md`: Product vision, user stories, success metrics.
  - `SaaS-TDD.md`: Architecture, Laravel packages, database schema, sprint plan.
  - `ai-integration-plan.md`: Integration with Bible Indexing, News Monitoring, and RAG systems.
  - `Teams-implementation.md`: Jetstream teams, subscriptions, admin billing.
  - `saas-requirements.md`: Functional and non-functional requirements.

#### Content Site Map
- **Location**: `documentation/content-site-map`
- **Purpose**: Defines frontend pages, API endpoints, and navigation for UI and API development.

#### Laravel Packages
- **Location**: `documentation/laravel-packages.md`
- **Purpose**: Lists essential and optional packages, their purposes, and inclusion status.

#### Troubleshooting
- **Location**: `ts-docs` (e.g., `ts-docs/ISSUE-XXX`)
- **Purpose**: Logs issue-specific analysis, implementation, and task tracking per `troubleshooting.mdc`.

### Example AI Prompts
Below are sample prompts for AI agents to create tasks using referenced documentation:

#### Summarize Requirements
- **Prompt**: "Using `saas-prd.md` and `saas-requirements.md` in `documents/PlanningDocs`, provide a summary of the SermonAssist MVP features and non-functional requirements, including accessibility and security standards."
- **Purpose**: Helps AI understand project scope and compliance needs.

#### Generate Backend Code
- **Prompt**: "Based on `SaaS-TDD.md` and `Teams-implementation.md` in `documents/PlanningDocs`, create a Laravel controller for the `POST /api/teams/{team}/sermons` endpoint, ensuring PSR-12 compliance and team-based authorization as per `documents/developmentGuidelines`."
- **Purpose**: Produces secure, standards-compliant backend code.

#### Implement AI Integration
- **Prompt**: "Referencing `ai-integration-plan.md` in `documents/PlanningDocs`, write a Laravel service class to fetch verse data from the Bible Indexing System API, incorporating Redis caching and error handling as specified."
- **Purpose**: Facilitates AI-driven feature development.

#### Design Frontend Component
- **Prompt**: "Using `documentation/content-site-map` and `SaaS-TDD.md`, create a Vue 3 component for the sermon workspace, ensuring WCAG 2.1 AA compliance as outlined in `documents/developmentGuidelines/AI Development Guidelines for Additional Cursor Rules`."
- **Purpose**: Guides UI development with accessibility in mind.

#### Troubleshoot Issue
- **Prompt**: "Check `ts-docs` and `laravel-packages.md` in `documentation` to diagnose a Jetstream teams authentication failure, referencing `Teams-implementation.md` for team-based permissions."
- **Purpose**: Assists in resolving technical issues.

AI agents should adapt these prompts to specific tasks, ensuring references to the correct documentation for accuracy.

## 8. Merge Request (MR) Reviews

### Overview
Merge requests (MRs) ensure code quality, security, and alignment with project goals. All MRs must follow a standardized review process.

### MR Submission Guidelines
- **Title**: Clear, descriptive (e.g., "Add sermon creation endpoint").
- **Description**: Include issue reference, changes, and testing instructions.
- **Commits**: Follow Git guidelines in `documents/developmentGuidelines`.
- **Tests**: Include unit/feature tests for new functionality.

### Review Process
- **Reviewers**: Minimum of two reviewers (one backend, one frontend if applicable).
- **Checklist**:
  - Code adheres to PSR-12 and Vue/TypeScript standards.
  - Tests pass and cover new functionality.
  - Accessibility (WCAG 2.1 AA) and security protocols are followed.
  - Documentation is updated if needed.
- **Tools**: Use GitHub Pull Request reviews with comments and suggestions.

### Approval Criteria
- All reviewer comments addressed.
- CI/CD pipelines (GitHub Actions) pass.
- No unresolved security or accessibility issues.

See `documents/developmentGuidelines` for detailed Git and review workflows.

## 9. License

### Overview
The SermonAssist platform is open-sourced under the [MIT License](https://opensource.org/licenses/MIT), allowing flexible use, modification, and distribution.

### Security Vulnerabilities
Report vulnerabilities to [security@sermonassist.com](mailto:security@sermonassist.com). Issues will be addressed promptly to ensure platform security.