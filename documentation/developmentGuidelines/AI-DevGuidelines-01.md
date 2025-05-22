# AI Development Guidelines for Applying Cursor Rules

This document provides guidelines for an AI to interpret and apply the cursor rules for developing a Laravel, Jetstream, Inertia.js, and Vue-based application with TypeScript, focusing on testing, accessibility, and performance. These guidelines ensure that generated code, tests, and documentation adhere to the specified standards for consistency, maintainability, and quality.

## Overview

The cursor rules cover several key areas:
- **Vue Testing**: Guidelines for testing Vue components using Vue Test Utils.
- **Cypress Testing**: Rules for end-to-end testing with Cypress, focusing on user flows.
- **Accessibility**: Standards for ensuring UI components meet WCAG 2.1 AA accessibility requirements.
- **Performance**: Best practices for optimizing Laravel and Vue applications.
- **Laravel**: Guidelines for PHP, Laravel, Jetstream, and Inertia.js development.
- **Cypress Vue Testing Attributes**: Rules for adding `data-cy` and ARIA attributes for testing and accessibility.
- **Component Structure**: Structure and organization of Vue components.
- **TypeScript Code Style**: TypeScript conventions for frontend code.
- **PHP Testing**: Guidelines for unit and feature tests in Laravel.
- **Vue/Inertia Code Style**: Coding standards for Vue components with Inertia.js integration.

The AI should reference these rules when generating code, tests, or documentation to ensure compliance with project standards.

## General Principles for AI Application

1. **Context Awareness**: Before generating code or tests, analyze the file type and location (e.g., `resources/js/Components/**/*.vue`, `tests/**/*.php`) to determine which rules apply based on the `globs` patterns in the cursor rules.
2. **Default Behavior**: Apply rules marked `alwaysApply: true` (e.g., Laravel rules) universally unless overridden. For rules with `alwaysApply: false`, apply only to matching file patterns.
3. **Artifact Generation**: Wrap all generated code, documentation, or other artifacts in an `<xaiArtifact>` tag with a unique UUID `artifact_id`, appropriate `title`, and correct `contentType` (e.g., `text/html`, `text/typescript`, `text/php`, `text/latex`).
4. **Incremental Updates**: If modifying an existing artifact, reuse the original `artifact_id` and update only the requested sections, preserving unchanged content.
5. **Error Handling**: Validate generated code for syntax errors and adherence to rules (e.g., PSR-12 for PHP, TypeScript strict mode). Log any deviations for review.
6. **Type Safety**: Use TypeScript with strict settings for frontend code and PHP 8.1+ with strict typing for backend code.

## Specific Guidelines by Rule Set

### 1. Vue Testing Rules (`vue-testing.mdc`)

**Scope**: Applies to `resources/js/__tests__/**/*.spec.ts` and `resources/js/__tests__/**/*.test.ts`.

**Guidelines**:
- **Component Testing**:
  - Generate tests for all new Vue components using Vue Test Utils.
  - Use `shallowMount` for isolated testing and `mount` when child components are relevant.
  - Test props, events, slots, lifecycle methods, and user interactions (e.g., clicks, form submissions).
  - Mock dependencies and Inertia.js using appropriate mocking libraries.
  - Example test structure:
    ```typescript
    import { shallowMount } from '@vue/test-utils';
    import ComponentName from '@/Components/ComponentName.vue';

    describe('ComponentName', () => {
      it('renders with correct props', () => {
        const wrapper = shallowMount(ComponentName, {
          props: { propName: 'value' }
        });
        expect(wrapper.exists()).toBe(true);
        expect(wrapper.props('propName')).toBe('value');
      });

      it('emits event on button click', async () => {
        const wrapper = shallowMount(ComponentName);
        await wrapper.find('[data-cy="component-button"]').trigger('click');
        expect(wrapper.emitted('customEvent')).toBeTruthy();
      });
    });
    ```
- **Assertions**:
  - Use `wrapper.exists()`, `wrapper.text()`, `wrapper.html()`, `wrapper.props()`, `wrapper.emitted()`, and `wrapper.classes()` for assertions.
  - Await async operations with `flushPromises()` for reactivity tests.
- **Best Practices**:
  - Organize tests by component behavior (e.g., rendering, interactions).
  - Use `beforeEach` for common setup and reset mocks between tests.
  - Use data factories for consistent test data.

### 2. Cypress Testing Rules (`cypress.mdc`)

**Scope**: Applies to `cypress/e2e/**/*.cy.ts`, `cypress/integration/**/*.spec.ts`, `cypress/support/**/*.ts`.

**Guidelines**:
- **Test Structure**:
  - Generate Cypress tests for complete user flows (e.g., login, form submission).
  - Use `describe` and `context` blocks to group related scenarios, and `it` blocks for individual test cases.
  - Example:
    ```typescript
    describe('Login Flow', () => {
      beforeEach(() => {
        cy.visit('/login');
      });

      it('allows user to log in with valid credentials', () => {
        cy.get('[data-cy="login-form-email-input"]').type('test@example.com');
        cy.get('[data-cy="login-form-password-input"]').type('password');
        cy.get('[data-cy="login-form-submit-button"]').click();
        cy.get('[data-cy="login-form-success-message"]').should('be.visible');
      });
    });
    ```
- **Selectors**:
  - Use `data-cy` attributes (e.g., `cy.get('[data-cy="login-form-email-input"]')`).
  - Avoid CSS selectors tied to styling; prefer semantic selectors or `cy.contains()` for text.
- **Data Management**:
  - Use fixtures for static data and factories for dynamic data.
  - Mock API responses with `cy.intercept()`.
  - Reset application state between tests using `cy.request()` or database cleanup.
- **Best Practices**:
  - Minimize `cy.wait()`; prefer waiting for specific events (e.g., `cy.get().should('be.visible')`).
  - Test both success and failure scenarios.
  - Enable screenshots on test failures and video recording.
  - Optimize test execution for CI/CD pipelines.

### 3. Accessibility Rules (`accessibility.mdc`)

**Scope**: Applies to `resources/js/Components/**/*.vue`, `resources/js/Pages/**/*.vue`, `resources/js/Layouts/**/*.vue`.

**Guidelines**:
- **General Accessibility**:
  - Ensure WCAG 2.1 AA compliance (e.g., 4.5:1 contrast ratio, keyboard navigability).
  - Add ARIA attributes (e.g., `aria-label`, `aria-labelledby`, `aria-hidden`) where HTML semantics are insufficient.
  - Support browser zoom up to 200% and test with screen readers (NVDA, VoiceOver).
- **Component Generation**:
  - Use semantic HTML (e.g., `<button>` for actions, `<a>` for navigation).
  - Add `data-cy` attributes for testing alongside ARIA attributes.
  - Example (modal component):
    ```vue
    <template>
      <div v-if="isOpen" role="dialog" aria-modal="true" aria-labelledby="modal-title" data-cy="confirmation-modal">
        <h2 id="modal-title" data-cy="modal-title">Confirm Action</h2>
        <button data-cy="modal-confirm-button" @click="confirm">Confirm</button>
        <button data-cy="modal-cancel-button" @click="close">Cancel</button>
      </div>
    </template>
    <script setup lang="ts">
    import { ref, watch } from 'vue';
    defineProps<{ isOpen: boolean }>();
    const emit = defineEmits(['confirm', 'close']);
    const modalRef = ref<HTMLElement | null>(null);
    watch(() => props.isOpen, (isOpen) => {
      if (isOpen) modalRef.value?.focus();
    });
    </script>
    ```
- **Focus Management**:
  - Implement focus traps for modals and dialogs.
  - Use `tabindex="0"` for custom interactive elements; avoid `tabindex > 0`.
  - Ensure focus returns to the trigger element after dialogs close.
- **Forms and Media**:
  - Associate labels with inputs using `for` and `id`.
  - Provide alt text for images and captions for media.
  - Mark required fields with `aria-required="true"`.

### 4. Performance Rules (`performance.mdc`)

**Scope**: Applies globally (no specific `globs`).

**Guidelines**:
- **Laravel**:
  - Use Laravel caching (e.g., `Cache::remember()`) for expensive queries.
  - Optimize database queries with proper indexing and Eloquent relationships.
  - Example:
    ```php
    use Illuminate\Support\Facades\Cache;
    public function getUsers() {
      return Cache::remember('users', 3600, function () {
        return User::with('roles')->get();
      });
    }
    ```
- **Vue**:
  - Implement lazy loading for components:
    ```typescript
    const MyComponent = defineAsyncComponent(() => import('./MyComponent.vue'));
    ```
  - Use code splitting and minimize Inertia payloads with partial reloads.
- **Assets**:
  - Minify CSS/JS, optimize images, and configure CDN caching.
  - Example Vite configuration:
    ```javascript
    export default defineConfig({
      build: {
        minify: 'esbuild',
        rollupOptions: {
          output: {
            manualChunks: { vendor: ['vue', 'pinia'] }
          }
        }
      }
    });
    ```
- **API**:
  - Use pagination and caching for API responses.
  - Example:
    ```php
    public function index() {
      return UserResource::collection(User::paginate(10));
    }
    ```

### 5. Laravel Rules (`laravel.mdc`)

**Scope**: Applies globally (`alwaysApply: true`).

**Guidelines**:
- **PHP/Laravel**:
  - Use PHP 8.1+ features (e.g., typed properties, match expressions).
  - Follow PSR-12 and use dependency injection:
    ```php
    declare(strict_types=1);
    class UserController {
      public function __construct(protected UserRepository $users) {}
      public function index(): JsonResponse {
        return response()->json($this->users->all());
      }
    }
    ```
  - Use Eloquent relationships, validation, and job queues.
- **Vue/Inertia**:
  - Use Vue 3 Composition API with `<script setup>` and TypeScript.
  - Leverage Inertia.js Links and Forms:
    ```vue
    <template>
      <InertiaLink href="/dashboard" data-cy="dashboard-link">Dashboard</InertiaLink>
    </template>
    ```
  - Use Pinia for state management and API resources for response transformation.

### 6. Cypress Vue Testing Attributes (`cypress-vue-testing.mdc`)

**Scope**: Applies to `resources/js/Components/**/*.vue`, `resources/js/Pages/**/*.vue`, `resources/js/Layouts/**/*.vue`.

**Guidelines**:
- Add `data-cy` attributes to all interactive elements using the format `[component]-[element-type]-[purpose]`:
  ```vue
  <button data-cy="user-profile-edit-button">Edit</button>
  ```
- Include ARIA attributes alongside `data-cy` for accessibility:
  ```vue
  <input data-cy="login-form-email-input" aria-label="Email address" type="email" />
  ```
- Ensure `data-cy` attributes are added to dynamic content, error messages, and state indicators.

### 7. Component Structure Rules (`component-structure.mdc`)

**Scope**: Applies to `resources/js/Components/**/*.vue`.

**Guidelines**:
- **Structure**:
  - Create single-responsibility components in separate files.
  - Use TypeScript with `<script setup>`:
    ```vue
    <script setup lang="ts">
    interface Props { user: IUserData }
    defineProps<Props>();
    </script>
    <template>
      <div data-cy="user-card">{{ user.name }}</div>
    </template>
    <style scoped>
    /* Tailwind CSS */
    </style>
    ```
- **Organization**:
  - Group related components in subdirectories (e.g., `Components/User/`).
  - Use naming conventions: `BaseButton`, `AppLayout`, `DashboardPage`.
- **Communication**:
  - Use props, emits, provide/inject, and Pinia appropriately.
  - Avoid prop drilling by using Pinia for global state.

### 8. TypeScript Code Style Rules (`typescript-code-style.mdc`)

**Scope**: Applies to `*.ts`, `resources/js/**/*.ts`, `resources/js/types/**/*.ts`, `resources/js/composables/**/*.ts`, `resources/js/utils/**/*.ts`.

**Guidelines**:
- **Typing**:
  - Use interfaces (e.g., `IUserData`) and explicit return types.
  - Avoid `any`; use specific types or generics.
  - Example:
    ```typescript
    interface IUserData { id: number; name: string }
    function getUser(id: number): Promise<IUserData> {
      return api.get(`/users/${id}`);
    }
    ```
- **Formatting**:
  - Use 2-space indentation, double quotes, and trailing commas.
  - Keep lines under 100 characters.
- **Best Practices**:
  - Use ES6+ features (e.g., destructuring, optional chaining).
  - Prefer `const` and async/await.

### 9. PHP Testing Rules (`php-testing.mdc`)

**Scope**: Applies to `tests/**/*.php`, `tests/Feature/**/*.php`, `tests/Unit/**/*.php`.

**Guidelines**:
- **Unit Tests**:
  - Test isolated classes with mocks:
    ```php
    public function test_user_repository_fetches_users(): void {
      $mock = $this->createMock(UserModel::class);
      $mock->expects($this->once())->method('all')->willReturn(collect([]));
      $repository = new UserRepository($mock);
      $this->assertEmpty($repository->all());
    }
    ```
- **Feature Tests**:
  - Test workflows with Laravel helpers:
    ```php
    public function test_user_can_login(): void {
      $user = User::factory()->create();
      $response = $this->actingAs($user)->get('/dashboard');
      $response->assertStatus(200);
    }
    ```
- **Database**:
  - Use in-memory SQLite and model factories.
  - Wrap tests in transactions using `RefreshDatabase` or `DatabaseTransactions`.

### 10. Vue/Inertia Code Style Rules (`vue-inertia-code-style.mdc`)

**Scope**: Applies to `*.vue`, `resources/js/**/*.vue`, `resources/js/Pages/**/*.vue`, `resources/js/Components/**/*.vue`, `resources/js/Layouts/**/*.vue`.

**Guidelines**:
- **Script Setup**:
  - Use `<script setup lang="ts">` with TypeScript:
    ```vue
    <script setup lang="ts">
    import { useForm } from '@inertiajs/vue3';
    const form = useForm({ email: '', password: '' });
    </script>
    ```
- **Template**:
  - Use shorthand directives (`:value`, `@click`) and self-closing tags.
  - Keep template expressions simple; use computed properties for complex logic.
- **Inertia.js**:
  - Use `InertiaLink` for navigation and `useForm` for forms.
  - Minimize payloads with partial reloads.

## Implementation Workflow for AI

1. **Analyze Request**:
   - Identify the type of artifact (e.g., Vue component, PHP controller, test file).
   - Check file path against `globs` to determine applicable rules.
2. **Generate Code**:
   - Apply relevant coding standards (e.g., TypeScript for Vue, PSR-12 for PHP).
   - Include `data-cy` and ARIA attributes for Vue components.
   - Use proper naming conventions and structure.
3. **Generate Tests**:
   - Create Vue Test Utils tests for components and Cypress tests for user flows.
   - Follow AAA or Given-When-Then patterns.
   - Mock dependencies and use factories/fixtures.
4. **Optimize Performance**:
   - Implement caching, lazy loading, and pagination as per performance rules.
5. **Ensure Accessibility**:
   - Add semantic HTML, ARIA attributes, and focus management.
   - Validate against WCAG 2.1 AA.
6. **Wrap in Artifact**:
   - Enclose generated content in an `<xaiArtifact>` tag with a unique UUID, title, and correct content type.
7. **Validate Output**:
   - Ensure syntax correctness and adherence to rules.
   - Log any deviations for manual review.

## Example Artifact Generation

For a request to create a Vue component with a login form:

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
interface Props { error?: string }
defineProps<Props>();
const form = useForm({
  email: '',
  password: ''
});
function submit() {
  form.post('/login');
}
</script>
<template>
  <form data-cy="login-form" @submit.prevent="submit">
    <label for="email" data-cy="login-form-email-label">Email</label>
    <input
      id="email"
      v-model="form.email"
      type="email"
      data-cy="login-form-email-input"
      aria-required="true"
    />
    <span v-if="error" data-cy="login-form-error">{{ error }}</span>
    <button
      type="submit"
      data-cy="login-form-submit-button"
      :disabled="form.processing"
    >
      Log In
    </button>
  </form>
</template>
<style scoped>
/* Tailwind CSS */
</style>
```

Corresponding Cypress test:
```typescript
describe('Login Page', () => {
  beforeEach(() => {
    cy.visit('/login');
  });
  it('submits login form with valid credentials', () => {
    cy.get('[data-cy="login-form-email-input"]').type('test@example.com');
    cy.get('[data-cy="login-form-submit-button"]').click();
    cy.get('[data-cy="login-form-success-message"]').should('be.visible');
  });
});
```

## Notes

- **Memory Management**: Store all generated artifacts in memory for reference. If a user requests to forget a chat, instruct them to use the UI book icon or disable memory in "Data Controls" settings.
- **Real-Time Updates**: If needed, search the web or X posts for additional context, but validate against the provided rules.
- **Error Reporting**: If a rule cannot be applied (e.g., missing dependency), include a comment in the code explaining the issue and suggesting a resolution.

This documentation ensures that an AI can systematically apply the cursor rules to produce high-quality, standards-compliant code and tests for a Laravel/Vue/Inertia.js application.