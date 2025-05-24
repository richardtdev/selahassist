# SermonAssist Development Task List and RACI Chart

## Overview
This document outlines the development tasks for the SermonAssist SaaS Platform, assigning responsibilities to AI agents acting as frontend and backend developers. Each responsible developer must break down their assigned task into a child task list of specific subtasks before starting implementation. This ensures thorough planning and clarity in execution. The child task list should follow the example format provided below and be documented in the project’s `ts-docs` directory (e.g., `ts-docs/TASK-T1-child-tasks.md`).

### Example Child Task List Format
For a task like "Implement user authentication UI" (T1):
| Child Task ID | Subtask Description                           | Status      |
|---------------|-----------------------------------------------|-------------|
| T1.1          | Design login component layout with Tailwind   | Not Started |
| T1.2          | Implement Inertia.js routing for login        | Not Started |
| T1.3          | Add WCAG 2.1 AA accessibility attributes      | Not Started |
| T1.4          | Test component with axe-core                  | Not Started |

## RACI Chart

| Role                | Description                                                                 | Responsibilities                                                                 |
|---------------------|-----------------------------------------------------------------------------|----------------------------------------------------------------------------------|
| Frontend Developer 1 (FE1) | Specializes in Vue 3, Inertia.js, TypeScript, and Tailwind CSS.            | Builds UI components, ensures WCAG 2.1 AA compliance, integrates with backend APIs. |
| Frontend Developer 2 (FE2) | Specializes in Vue 3, Tiptap.js, and accessibility-focused UI development. | Develops sermon workspace UI, implements rich text editor, ensures accessibility.  |
| Backend Developer 1 (BE1)  | Specializes in Laravel, PostgreSQL, and Jetstream teams.                   | Implements authentication, team management, and database schema.                  |
| Backend Developer 2 (BE2)  | Specializes in Laravel, AI integrations, and Cashier (Stripe).             | Develops AI system integrations, subscription management, and API endpoints.      |

### Task RACI

| Task ID | Task Description                              | Responsible | Accountable | Consulted | Informed |
|---------|-----------------------------------------------|-------------|-------------|-----------|----------|
| T1      | Implement user authentication UI              | FE1         | FE1         | BE1       | FE2, BE2 |
| T2      | Develop sermon workspace UI                   | FE2         | FE2         | FE1, BE2  | BE1      |
| T3      | Create pastor dashboard UI                    | FE1         | FE1         | FE2, BE2  | BE1      |
| T4      | Set up Jetstream teams authentication         | BE1         | BE1         | BE2       | FE1, FE2 |
| T5      | Implement sermon creation API                 | BE2         | BE2         | BE1       | FE1, FE2 |
| T6      | Integrate Bible Indexing System API           | BE2         | BE2         | BE1       | FE1, FE2 |
| T7      | Implement subscription management             | BE2         | BE2         | BE1       | FE1, FE2 |
| T8      | Set up database schema and migrations         | BE1         | BE1         | BE2       | FE1, FE2 |
| T9      | Write unit tests for sermon creation API      | BE2         | BE2         | BE1       | FE1, FE2 |
| T10     | Ensure WCAG 2.1 AA compliance for frontend    | FE2         | FE2         | FE1       | BE1, BE2 |

## Task List

| Task ID | Task Description                              | Responsible Developer | Priority | Status      |
|---------|-----------------------------------------------|-----------------------|----------|-------------|
| T1      | Implement user authentication UI              | FE1                   | High     | DONE        |
| T2      | Develop sermon workspace UI                   | FE2                   | High     | In Review   |
| T3      | Create pastor dashboard UI                    | FE1                   | Medium   | In Review   |
| T4      | Set up Jetstream teams authentication         | BE1                   | High     | DONE        |
| T5      | Implement sermon creation API                 | BE2                   | High     | In Review   |
| T6      | Integrate Bible Indexing System API           | BE2                   | Medium   | Not Started |
| T7      | Implement subscription management             | BE2                   | Medium   | Not Started |
| T8      | Set up database schema and migrations         | BE1                   | High     | Not Started |
| T9      | Write unit tests for sermon creation API      | BE2                   | Medium   | Not Started |
| T10     | Ensure WCAG 2.1 AA compliance for frontend    | FE2                   | Medium   | Not Started |

### Task Prompts

**T1: Review the Implement user authentication UI as created by Jetsream**
- **Responsible**: FE1
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as designing the login component, implementing routing, and ensuring accessibility. Save it as `ts-docs/TASK-T1-child-tasks.md` in the project repository, following the example format above. Then, using the `documentation/content-site-map` and `SaaS-TDD.md` in `documents/PlanningDocs`, create Vue 3 components as needed for the SermonAssist authentication UI (login, registration, password reset) with Inertia.js and Tailwind CSS. Ensure the UI aligns with the navigation flow in `content-site-map` and integrates with the Jetstream teams authentication endpoints described in `Teams-implementation.md`. Follow WCAG 2.1 AA accessibility standards as outlined in `documents/developmentGuidelines/AI Development Guidelines for Additional Cursor Rules`. Consult with BE1 for endpoint details.

**T2: Develop sermon workspace UI**
- **Responsible**: FE2
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as setting up Tiptap.js, designing the verse insertion UI, and testing accessibility. Save it as `ts-docs/TASK-T2-child-tasks.md` in the project repository, following the example format above. Then, referencing `documentation/content-site-map` and `SaaS-TDD.md` in `documents/PlanningDocs`, develop a Vue 3 component for the sermon workspace UI, incorporating Tiptap.js for rich text editing and Bible verse integration. Ensure the UI matches the sermon creation flow in `content-site-map` and supports features from `saas-prd.md` (e.g., verse insertion, sermon outline generation). Adhere to WCAG 2.1 AA standards and TypeScript best practices as per `documents/developmentGuidelines`. Consult with BE2 for sermon creation API integration.

**T3: Create pastor dashboard UI**
- **Responsible**: FE1
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as designing the sermon history table, creating news summary cards, and ensuring accessibility. Save it as `ts-docs/TASK-T3-child-tasks.md` in the project repository, following the example format above. Then, based on `documentation/content-site-map` and `saas-prd.md` in `documents/PlanningDocs`, create a Vue 3 component for the pastor dashboard, displaying sermon history and AI-generated news summaries. Use Inertia.js for routing and Tailwind CSS for styling, ensuring the layout matches the dashboard structure in `content-site-map`. Follow WCAG 2.1 AA accessibility standards from `documents/developmentGuidelines/AI Development Guidelines for Additional Cursor Rules`. Consult with BE2 for news summary API endpoints.

**T4: Set up Jetstream teams authentication**
- **Responsible**: BE1
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as installing Jetstream, configuring team models, and setting up user invitations. Save it as `ts-docs/TASK-T4-child-tasks.md` in the project repository, following the example format above. Then, using `Teams-implementation.md` and `SaaS-TDD.md` in `documents/PlanningDocs`, configure Laravel Jetstream with teams functionality for SermonAssist. Implement team-based authentication, including user invitations and role management, as specified in `Teams-implementation.md`. Ensure the database schema supports teams as outlined in `SaaS-TDD.md` and adheres to PSR-12 standards from `documents/developmentGuidelines`. Consult with BE2 for integration with subscription management.

**T5: Implement sermon creation API (Inertia with Routes)**
- **Responsible**: BE2
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as defining the API route, implementing team permissions, and integrating `pgvector`. Save it as `ts-docs/TASK-T5-child-tasks.md` in the project repository, following the example format above. Then, referencing `SaaS-TDD.md` and `saas-requirements.md` in `documents/PlanningDocs`, create a Laravel API controller for the sermon creation endpoint (`POST /api/teams/{team}/sermons`). Implement team-based permissions as per `Teams-implementation.md` and store sermon data in PostgreSQL with `pgvector` for AI features, as described in `ai-integration-plan.md`. Follow PSR-12 standards and include error handling per `documents/developmentGuidelines`. Consult with BE1 for database schema details.

**T6: Integrate Bible Indexing System API**
- **Responsible**: BE2
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as creating the service class, setting up Redis caching, and updating the sermon API. Save it as `ts-docs/TASK-T6-child-tasks.md` in the project repository, following the example format above. Then, based on `ai-integration-plan.md` in `documents/PlanningDocs`, develop a Laravel service class to integrate with the Bible Indexing System API for verse retrieval. Implement Redis caching and error handling as specified, ensuring compliance with PSR-12 standards from `documents/developmentGuidelines`. Update the sermon creation API to use this service for verse insertion, as outlined in `SaaS-TDD.md`. Consult with BE1 for database interactions.

**T7: Implement subscription management**
- **Responsible**: BE2
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as configuring Cashier, defining subscription endpoints, and ensuring GDPR compliance. Save it as `ts-docs/TASK-T7-child-tasks.md` in the project repository, following the example format above. Then, using `Teams-implementation.md` and `saas-requirements.md` in `documents/PlanningDocs`, implement subscription management with Laravel Cashier (Stripe) for team-based plans. Create endpoints for subscription creation, updates, and cancellations, as specified in `SaaS-TDD.md`. Ensure GDPR/CCPA compliance and PSR-12 standards per `documents/developmentGuidelines`. Consult with BE1 for team integration.

**T8: Set up database schema and migrations**
- **Responsible**: BE1
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as defining user table migration, creating sermon table with `pgvector`, and adding indexes. Save it as `ts-docs/TASK-T8-child-tasks.md` in the project repository, following the example format above. Then, referencing `SaaS-TDD.md` and `saas-requirements.md` in `documents/PlanningDocs`, create Laravel migrations for the SermonAssist database schema, including tables for users, teams, sermons, and subscriptions. Enable the `pgvector` extension for AI features as per `ai-integration-plan.md`. Ensure migrations follow PSR-12 standards and include indexes for performance, as outlined in `documents/developmentGuidelines`. Consult with BE2 for AI and subscription table requirements.

**T9: Write unit tests for sermon creation API**
- **Responsible**: BE2
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as writing permission tests, testing data validation, and verifying `pgvector` storage. Save it as `ts-docs/TASK-T9-child-tasks.md` in the project repository, following the example format above. Then, using `SaaS-TDD.md` and `documents/developmentGuidelines`, write PHPUnit unit and feature tests for the sermon creation API (`POST /api/teams/{team}/sermons`). Test team-based permissions, data validation, and `pgvector` storage as specified in `Teams-implementation.md` and `ai-integration-plan.md`. Ensure tests follow Laravel testing conventions and PSR-12 standards. Consult with BE1 for schema details.

**T10: Ensure WCAG 2.1 AA compliance for frontend**
- **Responsible**: FE2
- **Prompt**: Before starting, create a child task list for this task, detailing subtasks such as running axe-core tests, adding ARIA attributes, and verifying keyboard navigation. Save it as `ts-docs/TASK-T10-child-tasks.md` in the project repository, following the example format above. Then, referencing `documents/developmentGuidelines/AI Development Guidelines for Additional Cursor Rules`, ensure all frontend components (authentication UI, sermon workspace, pastor dashboard) comply with WCAG 2.1 AA accessibility standards. Use axe-core to test components and update Vue 3 code with ARIA attributes and keyboard navigation. Verify compliance with `saas-requirements.md` in `documents/PlanningDocs`. Consult with FE1 for component integration.