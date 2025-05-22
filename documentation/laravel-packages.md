## Development Utilities

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper) | IDE autocompletion | Improve development experience | No - Separate Package |
| [Laravel Tinker](https://github.com/laravel/tinker) | REPL for Laravel | Interactive console for debugging | Included in Laravel Core |
| [Laravel Sail](https://laravel.com/docs/sail) | Docker development | Local development environment | Included in Laravel Core |# Laravel Packages for SermonAssist SaaS Platform

This document provides a curated list of Laravel packages that could be useful for the SermonAssist platform, organized by category. Each package includes information about whether it's already included in Laravel core or Jetstream, which helps with planning the development stack.

## MVP Essential Packages

These packages are considered essential for the initial SermonAssist MVP based on the PRD requirements:

1. **Laravel Jetstream** - Authentication, teams, and scaffolding
2. **Laravel Cashier (Stripe)** - Subscription billing
3. **Spatie Laravel Permission** - Role management
4. **pgvector-laravel** - Vector database operations for AI features
5. **Tiptap Editor** - Rich text editor for sermon creation
6. **Laravel Sentry** - Error tracking (for self-hosted Sentry)
7. **Laravel Backup** - Content and database backups



## Authentication & User Management

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel Jetstream](https://jetstream.laravel.com/) | Authentication scaffolding with team management | Core authentication, 2FA, email verification, profile management | Separate Package (Will Install) |
| [Laravel Fortify](https://github.com/laravel/fortify) | Backend for auth without opinions on UI | If headless auth is needed without Jetstream's frontend | Included in Jetstream |
| [Laravel Sanctum](https://laravel.com/docs/sanctum) | Lightweight API token authentication | API authentication for internal services and external integrations | Included in Jetstream |
| [Spatie Laravel Permission](https://github.com/spatie/laravel-permission) | Role and permission management | Role-based access for pastor, admin roles and future team roles | No - Separate Package |

## Subscription & Billing

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel Cashier (Stripe)](https://laravel.com/docs/billing) | Stripe subscription billing | Process payments, manage subscriptions, handle webhooks | No - Separate Package |
| [Laravel Invoice](https://github.com/LaravelDaily/laravel-invoices) | PDF invoice generation | Generate professional invoices for subscribers | No - Separate Package |

## Frontend & UI

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Inertia.js](https://inertiajs.com/) | SPA without API boilerplate | Server-driven frontend with Vue.js | Included in Jetstream (with Vue option) |
| [Tiptap Editor](https://tiptap.dev/) | Rich text editor for Vue | Sermon editing interface | No - Separate Package |
| [Ziggy](https://github.com/tighten/ziggy) | Use Laravel routes in JavaScript | Simplify route management in Inertia/Vue | No - Separate Package |
| [Laravel Mix](https://laravel-mix.com/) | Webpack API for Laravel | Asset compilation | Included in Laravel Core |
| [Tailwind CSS](https://tailwindcss.com/) | Utility-first CSS framework | Responsive design with minimal CSS | Included in Jetstream |
| [Vite](https://vitejs.dev/) | Frontend build tool | Modern alternative to Laravel Mix | Included in Laravel 9+ (replaced Mix) |

## Data & Database

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel PostgreSQL](https://github.com/laravel/framework/tree/8.x/src/Illuminate/Database/PostgresConnection) | PostgreSQL driver | Core database with pgvector support | Included in Laravel Core |
| [pgvector-laravel](https://github.com/pgvector/pgvector-laravel) | Laravel pgvector integration | Vector operations for embeddings | No - Separate Package |
| [Laravel Scout](https://laravel.com/docs/scout) | Full-text search | Search across sermons and content | No - Separate Package |
| [Eloquent-Sluggable](https://github.com/spatie/laravel-sluggable) | Create slugs for models | SEO-friendly URLs for sermons | No - Separate Package |
| [Laravel Auditing](https://github.com/owen-it/laravel-auditing) | Model change history | Track changes to sermons and templates | No - Separate Package |
| [Laravel Backup](https://github.com/spatie/laravel-backup) | Database/file backup | Regular backups of sermon content | No - Separate Package |
| [Laravel Excel](https://github.com/SpartnerNL/Laravel-Excel) | Excel/CSV imports/exports | Data import/export functionality | No - Separate Package |

## API & Integration

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel HTTP Client](https://laravel.com/docs/http-client) | Wrapper for Guzzle | API calls to Bible Indexing and News systems | Included in Laravel Core |
| [Laravel SDK for Stripe API](https://github.com/stripe/stripe-php) | Stripe API SDK | Used by Cashier for payment processing | Included with Cashier |
| [Laravel Rate Limiting](https://laravel.com/docs/routing#rate-limiting) | API rate limiting | Protect APIs from abuse | Included in Laravel Core |

## AI & Content Generation

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel OpenAI](https://github.com/openai-php/laravel) | OpenAI API integration | Optional integration for content generation | No - Separate Package |
| [PHP-AI/Sentence-Transformers](https://github.com/php-ai/php-ml) | PHP ML Library | Optional alternative for embedding generation | No - Separate Package |
| [Laravel Hugging Face](https://github.com/kambo-1st/hugging-face-php) | Hugging Face integration | Integration with open-source AI models | No - Separate Package |
| [PHP-ML](https://github.com/php-ai/php-ml) | Machine learning in PHP | Basic ML functionality if needed | No - Separate Package |
| [Xenon/LaravelBM25](https://github.com/m-a-k-o/laravel-bm25) | BM25 ranking algorithm | Alternative ranking for sermon search | No - Separate Package |
| [Feed Parser](https://github.com/willvincent/feeds) | RSS/Atom feed parser | Fetching news content from sources | No - Separate Package |

## Performance & Caching

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel Redis](https://laravel.com/docs/redis) | Redis integration | Caching and queue management | Included in Laravel Core |
| [Laravel Cache](https://laravel.com/docs/cache) | Cache abstraction | Built-in caching | Included in Laravel Core |
| [Laravel Page Cache](https://github.com/spatie/laravel-responsecache) | Full page caching | Cache entire responses for performance | No - Separate Package |

## Security & Compliance

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel GDPR](https://github.com/justbetter/laravel-gdpr) | GDPR compliance | Data export/deletion capabilities | No - Separate Package |
| [Laravel Security](https://github.com/enlightn/enlightn) | Security analysis | Scan for security vulnerabilities | No - Separate Package |
| [Laravel Sanitize](https://github.com/Waavi/Sanitizer) | Input sanitization | Clean user input for sermons | No - Separate Package |
| [Laravel Two Factor Auth](https://github.com/thecodework/two-factor-authentication) | 2FA implementation | Alternative to Jetstream 2FA | Included in Jetstream |
| [Laravel CSP](https://github.com/spatie/laravel-csp) | Content Security Policy | Enhanced XSS protection | No - Separate Package |
| [Laravel CORS](https://github.com/fruitcake/laravel-cors) | CORS middleware | Cross-origin request handling | Included in Laravel Core |

## Monitoring & Logging

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel Telescope](https://laravel.com/docs/telescope) | Debug assistant | Development debugging tool | No - Separate Package |
| [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) | Debug toolbar | Local development performance analysis | No - Separate Package |
| [Laravel Sentry](https://github.com/getsentry/sentry-laravel) | Error tracking | Integration with self-hosted Sentry | No - Separate Package |

## Testing & Quality Assurance

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [PHPUnit](https://phpunit.de/) | PHP testing framework | Backend unit and feature testing | Included in Laravel Core |
| [Larastan](https://github.com/nunomaduro/larastan) | Static analysis | Type checking for PHP code | No - Separate Package |
| [Laravel Pint](https://laravel.com/docs/pint) | PHP code style fixer | Enforce consistent code style | Included in Laravel 9+ |
| [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) | Code style fixer | Alternative style enforcement | No - Separate Package |

## Administration

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel Impersonate](https://github.com/404labfr/laravel-impersonate) | User impersonation | Admin troubleshooting of user accounts | No - Separate Package |
| [Laravel Activity Log](https://github.com/spatie/laravel-activitylog) | User activity tracking | Monitor user actions for auditing | No - Separate Package |

## Internationalization & Localization

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel Translatable](https://github.com/spatie/laravel-translatable) | Model translations | Multi-language support for content | No - Separate Package |
| [Laravel Localization](https://github.com/mcamara/laravel-localization) | Route localization | Multi-language URL support | No - Separate Package |
| [Laravel Translator](https://github.com/vinkla/translator) | Model translator | Alternative translation package | No - Separate Package |
| [Laravel Translation Loader](https://github.com/spatie/laravel-translation-loader) | Database translations | Store translations in database | No - Separate Package |
| [Laravel Translation Manager](https://github.com/barryvdh/laravel-translation-manager) | Translation UI | Interface for managing translations | No - Separate Package |
| [Laravel Lang](https://github.com/Laravel-Lang/lang) | Language files | Pre-translated language files | No - Separate Package |

## Accessibility & SEO

| Package | Description | Purpose in SermonAssist | Included In |
|---------|-------------|------------------------|-------------|
| [Laravel SEO](https://github.com/artesaos/seotools) | SEO tools | META tags, sitemap, OpenGraph | No - Separate Package |
| [Laravel Sitemap](https://github.com/spatie/laravel-sitemap) | Sitemap generation | Generate XML sitemaps | No - Separate Package |
| [Laravel Meta](https://github.com/kodeine/laravel-meta) | Meta tag manager | Manage page meta information | No - Separate Package |
| [Eloquent Taggable](https://github.com/spatie/laravel-tags) | Tagging functionality | Add tags to sermons for organization | No - Separate Package |
| [Laravel Cookie Consent](https://github.com/spatie/laravel-cookie-consent) | Cookie consent | GDPR-compliant cookie notices | No - Separate Package |

## Development Utilities

| Package | Description | Purpose in SermonAssist |
|---------|-------------|------------------------|
| [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper) | IDE autocompletion | Improve development experience |
| [Laravel Tinker](https://github.com/laravel/tinker) | REPL for Laravel | Interactive console for debugging |
| [Laravel Sail](https://laravel.com/docs/sail) | Docker development | Local development environment |
| [Laravel Shift](https://laravelshift.com/) | Laravel upgrader | Assist with future version upgrades |
| [Laravel Module](https://github.com/nwidart/laravel-modules) | Modular application | Organize code into modules |
| [Laravel TypeScript Generator](https://github.com/kalnoy/laravel-typescript-generator) | TS definitions | Generate TypeScript types from models |

## Notes on AI Integration

The AI integration for SermonAssist will be handled separately from the SaaS platform, but the Laravel application will need to communicate with these AI services. Here's how this will work:

1. **API Integration**: The Laravel application will use the HTTP Client to make API calls to the Bible Indexing System, News Monitoring System, and RAG System

2. **Data Format**: JSON will be used for communication between the Laravel SaaS and AI systems

3. **Future Integration**: The architecture is designed to allow future direct integration of AI capabilities into the Laravel application if needed

4. **Authentication**: API keys will be used to secure communication between systems

This approach allows for independent development and scaling of the AI components while maintaining a clean separation of concerns in the architecture.
