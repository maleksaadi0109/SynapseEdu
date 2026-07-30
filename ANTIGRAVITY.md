# ANTIGRAVITY Project Rules & Architectural Guidelines

This document defines the core architecture, development standards, and code guidelines for **EduBridge AI (SynapseEdu)** backend when working with Antigravity AI agents.

---

## 1. Project Overview

- **Name**: EduBridge AI Backend (SynapseEdu)
- **Domain**: Offline-first educational platform with AI-assisted grading, RAG tutoring, and real-time delta synchronization.
- **Tech Stack**:
  - **Framework**: Laravel 13 (PHP 8.3+)
  - **Database**: PostgreSQL with `pgvector` extension
  - **Real-Time**: Laravel Reverb (WebSockets)
  - **Runtime & Deployment**: Laravel Octane / FrankenPHP, Docker
  - **AI Integration**: Laravel AI SDK, RAG vector embeddings, Fleiss' Kappa essay evaluation
  - **Testing & Quality**: Pest PHP, Larastan / PHPStan, Laravel Pint

---

## 2. Architectural Guidelines & Conventions

### 2.1 Application Layer Architecture (Controllers, Requests, Actions, Resources)

To maintain a clean, testable, and modular architecture, adhere strictly to the following layered separation of concerns:

#### 1. Controllers (`app/Http/Controllers/*`)
- **Keep Controllers Thin**: Controllers must only handle HTTP-level concerns: receiving requests, invoking Action/Service classes, and returning HTTP responses.
- **Single Responsibility**: Prefer Invokable / Single-Action Controllers (`__invoke`) for specialized endpoints (e.g. `RegisterController`), or standard RESTful controllers (`index`, `store`, `show`, `update`, `destroy`).
- **Strict Return Types**: Always specify explicit return types (`JsonResponse`, `JsonResource`, `AnonymousResourceCollection`). Never perform direct business logic or inline validation inside controller methods.

#### 2. Form Requests (`app/Http/Requests/*`)
- **Validation Isolation**: All incoming HTTP mutation requests (POST, PUT, PATCH) must use a dedicated `FormRequest` class.
- **Rules & Authorization**: Enforce field rules, unique constraints, and user permission authorization in `authorize()` and `rules()` methods.
- **Strong Typing**: Use typed data retrieval methods or DTOs downstream rather than accessing unvalidated `$request->all()`.

#### 3. Actions & Services (`app/Actions/*` & `app/Services/*`)
- **Actions**: Pure, single-task domain business logic (e.g. `App\Actions\RegisterUserAction`, `App\Actions\EvaluateEssayAction`). Executed via `execute()` or `__invoke()`.
- **Services**: Orchestration layers for multi-step or complex system integrations (e.g. `App\Services\SyncEngineService`, `App\Services\RagVectorSearchService`).
- **Reusability & Testability**: Actions/Services contain all database transactions, AI SDK interactions, and domain logic, allowing them to be easily tested in isolation.

#### 4. API Resources (`app/Http/Resources/*`)
- **Response Transformation**: Use Laravel `JsonResource` classes to transform Eloquent models into clean, structured JSON representations.
- **Security & Consistency**: Prevent leaking sensitive attributes (e.g. hashed passwords, internal keys). Ensure field names, nullability, and timestamp formats (ISO 8601) are consistent across all API endpoints.
- **Collections**: Use `JsonResource::collection($paginator)` or custom `ResourceCollection` classes for paginated or multi-record responses.

### 2.2 Offline-First Delta Synchronization (`/api/v1/sync`)
- **Sync Model Requirements**: Every syncable model (`Lesson`, `Quiz`, `Submission`, `Note`, `Assignment`) must track:
  - `id`: UUIDv4 primary key (generated on mobile or server).
  - `created_at`, `updated_at`: Standard ISO 8601 timestamps.
  - `deleted_at`: SoftDeletes timestamp for sync tracking.
  - `version`: Incrementing integer version counter for conflict tracking.
- **Pull Endpoint (`/api/v1/sync/pull`)**: Accepts `last_synced_at` timestamp and returns changes and soft-deleted IDs since that timestamp.
- **Push Endpoint (`/api/v1/sync/push`)**: Receives offline client payloads, resolves conflicts via last-write-wins or version fallback, and returns updated server timestamps.

### 2.3 Vector Database & AI Integration (RAG)
- Ensure PostgreSQL `vector` extension is installed.
- Embeddings are generated using the Laravel AI SDK and stored in vector columns.
- AI Essay Evaluation results must be validated against human rubrics and checked for agreement (Fleiss' Kappa metric).

---

## 3. Workflow & Quality Rules for Agents

When making changes to this codebase, agents **MUST** follow these rules:

1. **Do Not Swallowing Exceptions**: Never mask runtime errors or return dummy fallbacks silently. Always log or throw appropriate exceptions.
2. **Verify Code Changes**:
   - Run formatting: `vendor/bin/pint`
   - Run static analysis: `vendor/bin/phpstan` or `vendor/bin/larastan`
   - Run tests: `php artisan test`
3. **Preserve Documentation**: Maintain docstrings, standard comments, and existing API contracts across all modified files.
4. **File Naming**: Follow strict PascalCase for PHP class names and filenames (e.g. `RegisterController.php` instead of `Registercontroller.php`).

---

## 4. Useful Commands

```bash
# Code Formatting
vendor/bin/pint

# Static Analysis
vendor/bin/phpstan analyse

# Run Unit/Feature Tests
php artisan test

# Reverb WebSockets Server
php artisan reverb:start

# Octane Dev Server
php artisan octane:start
```
