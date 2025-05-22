# AI Development Guidelines for Additional Cursor Rules

This document provides guidelines for an AI to interpret and apply the additional cursor rules for developing a Laravel, Jetstream, Inertia.js, and Vue-based application with TypeScript, focusing on documentation, file organization, Git practices, PHP code style, security, and troubleshooting. These guidelines ensure that generated code, documentation, and workflows adhere to the specified standards for consistency, maintainability, and security.

## Overview

The additional cursor rules cover the following areas:
- **Documentation**: Standards for code, API, project, and component documentation.
- **File Organization**: Directory structure for frontend, backend, testing, and configuration files.
- **Git**: Rules for commit messages, branching, code reviews, and version control.
- **PHP Code Style**: Coding standards for PHP and Laravel-specific practices.
- **Security**: Guidelines for securing environment variables, authentication, data, and APIs.
- **Troubleshooting**: Structured approach to documenting and resolving issues.

The AI should reference these rules when generating code, documentation, or troubleshooting artifacts to ensure compliance with project standards.

## General Principles for AI Application

1. **Context Awareness**: Analyze the file type and location (e.g., `*.php`, `ts-docs/*`) to determine applicable rules based on `globs` patterns.
2. **Default Behavior**: Apply rules marked `alwaysApply: true` (e.g., Documentation rules) universally unless overridden. For rules with `alwaysApply: false`, apply only to matching file patterns.
3. **Artifact Generation**: Wrap all generated content in an `<xaiArtifact>` tag with a unique UUID `artifact_id`, appropriate `title`, and correct `contentType` (e.g., `text/php`, `text/markdown`).
4. **Incremental Updates**: If modifying an existing artifact, reuse the original `artifact_id` and update only the requested sections.
5. **Error Handling**: Validate generated code and documentation for syntax errors and adherence to rules (e.g., PSR-12 for PHP). Log deviations for review.
6. **Security and Documentation**: Prioritize security practices (e.g., sanitizing inputs) and comprehensive documentation for all generated artifacts.

## Specific Guidelines by Rule Set

### 1. Documentation Rules (`documentation.mdc`)

**Scope**: Applies globally (`alwaysApply: true`).

**Guidelines**:
- **Code Documentation**:
  - Document all public PHP methods and classes with PHPDoc comments:
    ```php
    /**
     * Retrieve all users from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsers(): Collection {
      return User::all();
    }
    ```
  - Use TypeScript interfaces for Vue component props and emits:
    ```typescript
    interface Props {
      user: IUserData;
    }
    ```
  - Document complex business logic with inline comments.
- **API Documentation**:
  - Generate documentation for all API endpoints using tools like Laravel API Documentation or Swagger.
  - Include request/response formats, authentication requirements, and examples:
    ```markdown
    # GET /api/users
    ## Description
    Retrieves a paginated list of users.
    ## Authentication
    Bearer token required.
    ## Response
    ```json
    {
      "data": [{ "id": 1, "name": "John Doe" }],
      "meta": { "current_page": 1 }
    }
    ```
    ```
- **Project Documentation**:
  - Update `README.md` with project setup, deployment, and architecture details.
  - Example `README.md` section:
    ```markdown
    # Project Setup
    1. Clone the repository: `git clone <repo-url>`
    2. Install dependencies: `composer install && npm install`
    3. Configure `.env`: `cp .env.example .env`
    4. Run migrations: `php artisan migrate`
    ```
- **Component Documentation**:
  - Document Vue component props, emits, usage, and examples:
    ```markdown
    # UserCard Component
    ## Props
    - `user: IUserData` - User data object
    ## Emits
    - `update-user` - Emitted when user data is updated
    ## Usage
    ```vue
    <UserCard :user="{ id: 1, name: 'John Doe' }" @update-user="handleUpdate" />
    ```
    ```
- **Best Practices**:
  - Use Markdown for documentation files in the `docs` directory.
  - Keep documentation organized and up to date with code changes.

### 2. File Organization Rules (`file-organization.mdc`)

**Scope**: Applies globally (no specific `globs`).

**Guidelines**:
- **Frontend Structure**:
  - Place Vue components in `resources/js/Components` (e.g., `UserCard.vue`).
  - Place layouts in `resources/js/Layouts` (e.g., `AppLayout.vue`).
  - Place Inertia pages in `resources/js/Pages` (e.g., `DashboardPage.vue`).
  - Place TypeScript types in `resources/js/types` (e.g., `types/user.ts`).
  - Place utility functions in `resources/js/utils` (e.g., `utils/formatDate.ts`).
  - Use `resources/js/app.ts` as the entry point.
- **Backend Structure**:
  - Place Laravel models in `app/Models` (e.g., `app/Models/User.php`).
  - Place controllers in `app/Http/Controllers` (e.g., `app/Http/Controllers/UserController.php`).
  - Place migrations in `database/migrations` (e.g., `database/migrations/2025_05_22_create_users_table.php`).
  - Place factories in `database/factories` (e.g., `database/factories/UserFactory.php`).
- **Testing Structure**:
  - Place feature tests in `tests/Feature` (e.g., `tests/Feature/UserTest.php`).
  - Place unit tests in `tests/Unit` (e.g., `tests/Unit/UserRepositoryTest.php`).
  - Place Vue tests in `resources/js/__tests__` (e.g., `resources/js/__tests__/UserCard.spec.ts`).
- **Configuration Files**:
  - Place environment variables in `.env` (referenced in `.env.example`).
  - Place frontend build config in `vite.config.ts` and TypeScript config in `tsconfig.json`.
- **AI Implementation**:
  - When generating files, place them in the correct directories based on their type.
  - Example file structure:
    ```
    resources/js/
    ├── Components/
    │   └── UserCard.vue
    ├── Pages/
    │   └── DashboardPage.vue
    ├── types/
    │   └── user.ts
    app/
    ├── Models/
    │   └── User.php
    ├── Http/
    │   └── Controllers/
    │       └── UserController.php
    ```

### 3. Git Rules (`git.mdc`)

**Scope**: Applies globally (no specific `globs`).

**Guidelines**:
- **Commit Messages**:
  - Use conventional commits (e.g., `feat: add user login form`, `fix: resolve null pointer in UserController`).
  - Reference issue numbers (e.g., `feat(ISSUE-123): implement login form`).
  - Keep commits atomic and focused on a single change.
- **Branching Strategy**:
  - Use feature branches (`feature/login-form`), bugfix branches (`bugfix/fix-login-error`), release branches (`release/v1.0.0`), and hotfix branches (`hotfix/v1.0.1`).
  - Follow Git flow for branching and merging.
- **Code Review**:
  - Generate pull requests for all changes with a focused scope.
  - Include a description linking to the issue and summarizing changes.
  - Example pull request description:
    ```markdown
    # Add User Login Form (ISSUE-123)
    ## Changes
    - Added `LoginPage.vue` with form and `data-cy` attributes.
    - Added Cypress tests in `cypress/e2e/login.cy.ts`.
    - Updated `README.md` with setup instructions.
    ```
- **Version Control**:
  - Tag releases (e.g., `git tag v1.0.0`).
  - Use `.gitignore` to exclude `.env`, `node_modules`, etc.
  - Use proper merge strategies (e.g., `git merge --no-ff` for feature branches).

### 4. PHP Code Style Rules (`php-code-style.mdc`)

**Scope**: Applies to `*.php`, `app/**/*.php`, `config/**/*.php`, `database/**/*.php`, `routes/**/*.php`, `tests/**/*.php`.

**Guidelines**:
- **General**:
  - Follow PSR-12 with strict typing:
    ```php
    declare(strict_types=1);
    namespace App\Http\Controllers;
    class UserController {
      public function index(): JsonResponse {
        return response()->json(User::all());
      }
    }
    ```
  - Keep methods small (< 20 lines) and avoid deep nesting.
- **Naming**:
  - Use PascalCase for classes, camelCase for methods/variables, and UPPER_SNAKE_CASE for constants.
  - Example:
    ```php
    const MAX_USERS = 100;
    public function getUserData(int $id): array {
      $userData = User::findOrFail($id);
      return $userData->toArray();
    }
    ```
- **Formatting**:
  - Use 4-space indentation, single spaces around operators, and max 120-character lines.
- **Laravel Specifics**:
  - Use dependency injection over facades:
    ```php
    public function __construct(protected UserRepository $users) {}
    ```
  - Use named routes and form requests:
    ```php
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    ```
- **Comments**:
  - Use PHPDoc for classes and methods:
    ```php
    /**
     * User Repository Interface
     */
    interface UserRepository {
      /**
       * Find a user by ID.
       *
       * @param int $id
       * @return User
       */
      public function find(int $id): User;
    }
    ```

### 5. Security Rules (`security.mdc`)

**Scope**: Applies globally (no specific `globs`).

**Guidelines**:
- **Environment Variables**:
  - Store sensitive data in `.env` and document in `.env.example`:
    ```env
    # .env.example
    APP_KEY=
    DB_PASSWORD=
    ```
  - Never commit `.env` files.
- **Authentication & Authorization**:
  - Use Laravel’s built-in authentication (e.g., Jetstream/Fortify).
  - Implement role-based access control with middleware:
    ```php
    Route::middleware('role:admin')->get('/admin', [AdminController::class, 'index']);
    ```
- **Data Security**:
  - Sanitize inputs using Laravel validation:
    ```php
    public function store(Request $request): JsonResponse {
      $validated = $request->validate([
        'email' => 'required|email',
        'name' => 'required|string|max:255'
      ]);
      // Process validated data
    }
    ```
  - Enable CSRF protection for forms and encrypt sensitive data.
- **API Security**:
  - Use Laravel Sanctum or Passport for API authentication.
  - Implement rate limiting:
    ```php
    Route::middleware('throttle:60,1')->group(function () {
      Route::get('/api/users', [UserController::class, 'index']);
    });
    ```
- **General**:
  - Keep dependencies updated using `composer update` and `npm update`.
  - Use HTTPS and proper error handling to avoid exposing sensitive information.

### 6. Troubleshooting Documentation Rules (`troubleshooting.mdc`)

**Scope**: Applies to `ts-docs/*` directories.

**Guidelines**:
- **Folder Structure**:
  - For each issue (e.g., `ISSUE-123`), create a folder `ts-docs/ISSUE-123` with three files: `analyze.md`, `implementation.md`, and `todo.md`.
  - Example structure:
    ```
    ts-docs/
    └── ISSUE-123/
        ├── analyze.md
        ├── implementation.md
        └── todo.md
    ```
- **Analysis Document (`analyze.md`)**:
  - Include issue summary, reproduction steps, error messages, and screenshots:
    ```markdown
    # ISSUE-123: Login Form Validation Failure
    ## Summary
    The login form does not display validation errors for invalid emails.
    ## Reproduction Steps
    1. Navigate to `/login`.
    2. Enter an invalid email (e.g., "invalid").
    3. Submit the form.
    ## Error Logs
    ```
    [2025-05-22 10:46:00] local.ERROR: Validation failed...
    ```
    ```
- **Implementation Document (`implementation.md`)**:
  - Document the solution, code changes, and testing procedures:
    ```markdown
    # Implementation for ISSUE-123
    ## Solution
    Added error display in `LoginPage.vue`.
    ## Code Changes
    ```vue
    <span v-if="errors.email" data-cy="login-form-email-error">{{ errors.email }}</span>
    ```
    ## Testing
    - Run `npm run test` to verify Vue component tests.
    - Run `cypress run` to verify end-to-end tests.
    ```
- **Todo Document (`todo.md`)**:
  - Use a checklist to track tasks:
    ```markdown
    # Todo for ISSUE-123
    - [x] Analyze error logs
    - [ ] Update `LoginPage.vue` with error display
    - [ ] Write Cypress test for error display
    - [ ] Deploy to staging
    ```
- **AI Workflow**:
  - Automatically create the folder structure when assigned a ticket.
  - Analyze error logs and suggest root causes in `analyze.md`.
  - Update `todo.md` as tasks are completed.
  - Cross-reference related documentation in the `docs` directory.

## Implementation Workflow for AI

1. **Analyze Request**:
   - Identify the artifact type (e.g., PHP controller, Vue component, documentation).
   - Check file path against `globs` to apply relevant rules.
2. **Generate Code**:
   - Follow PHP code style (PSR-12, strict typing) and file organization rules.
   - Include PHPDoc comments and TypeScript interfaces.
   - Place files in the correct directories (e.g., `app/Models`, `resources/js/Components`).
3. **Generate Documentation**:
   - Create comprehensive documentation for code, APIs, and components in the `docs` directory.
   - For troubleshooting, create `ts-docs/ISSUE-XXX` with `analyze.md`, `implementation.md`, and `todo.md`.
4. **Apply Security**:
   - Sanitize inputs, use middleware for authorization, and store sensitive data in `.env`.
5. **Git Practices**:
   - Generate atomic commits with conventional commit messages.
   - Create pull requests with detailed descriptions linking to issues.
6. **Wrap in Artifact**:
   - Enclose generated content in an `<xaiArtifact>` tag with a unique UUID, title, and correct content type.
7. **Validate Output**:
   - Ensure syntax correctness and adherence to PSR-12, TypeScript, and security rules.
   - Log deviations for manual review.

## Example Artifact Generation

For a request to create a secure API endpoint for user retrieval:

```php
<?php
declare(strict_types=1);
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserIndexRequest;

/**
 * User Controller
 */
class UserController extends Controller
{
    /**
     * Retrieve a paginated list of users.
     *
     * @param UserIndexRequest $request
     * @return JsonResponse
     */
    public function index(UserIndexRequest $request): JsonResponse
    {
        $users = User::paginate(10);
        return response()->json(UserResource::collection($users));
    }
}
```

Corresponding API documentation:
```markdown
# GET /api/users
## Description
Retrieves a paginated list of users.
## Authentication
Requires Bearer token with `admin` role.
## Request
- Query Parameters: `page` (optional, default: 1)
## Response
```json
{
  "data": [{ "id": 1, "name": "John Doe" }],
  "meta": { "current_page": 1, "per_page": 10 }
}
```
```

Troubleshooting documentation for an issue:
```markdown
# ts-docs/ISSUE-124/analyze.md
## Summary
API endpoint `/api/users` returns 500 error for invalid pagination.
## Reproduction Steps
1. Send GET request to `/api/users?page=invalid`.
2. Observe 500 error in response.
## Error Logs
```
[2025-05-22 10:46:00] local.ERROR: Invalid pagination parameter
```
```

## Notes

- **Memory Management**: Store all generated artifacts in memory. If a user requests to forget a chat, instruct them to use the UI book icon or disable memory in "Data Controls" settings.
- **Security Priority**: Always validate inputs and use secure practices (e.g., HTTPS, CSRF protection).
- **Documentation Updates**: Keep `README.md`, API, and component documentation up to date with code changes.
- **Error Reporting**: If a rule cannot be applied (e.g., missing configuration), include a comment explaining the issue and suggesting a resolution.

This documentation ensures that an AI can systematically apply the additional cursor rules to produce high-quality, secure, and well-documented code and artifacts for a Laravel/Vue/Inertia.js application.