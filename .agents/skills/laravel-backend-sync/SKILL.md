---
name: laravel-backend-sync
description: >-
  Provides architectural conventions, data patterns, and code instructions for building 
  Laravel 13 REST APIs, PostgreSQL pgvector integration, Laravel Reverb WebSockets, and 
  offline-first delta data synchronization (pull/push endpoints and conflict resolution).
---

# Laravel 13 & Offline Synchronization Architecture Skill

Use this skill when developing or refactoring backend features for EduBridge AI using Laravel 13.

---

## 1. Architectural Standards

- **Laravel 13 Conventions**:
  - Prefer Form Requests for validation instead of inline controller validation.
  - Use Service Classes & Actions for complex business logic (e.g. `SyncEngineService`, `EssayEvaluatorAction`).
  - Strict typing: Always declare return types and parameter types for PHP 8.3+.
  - Formatting & Quality: Run `vendor/bin/pint` for formatting, `vendor/bin/phpstan` / `larastan` for static analysis, and `php artisan test` (Pest PHP).

---

## 2. Delta Synchronization Protocol (Pull / Push)

### Database Schema Requirements for Sync Models
Every syncable model (`Lesson`, `Quiz`, `Submission`, `Note`, `Assignment`) must track:
- `id` (UUIDv4 preferred for offline generation on mobile client)
- `created_at` (Timestamp)
- `updated_at` (Timestamp)
- `deleted_at` (SoftDeletes Timestamp for sync tracking)
- `version` (Integer incrementing column or vector clock)

### Delta Pull Endpoint (`/api/v1/sync/pull`)
- **Query Parameter**: `last_synced_at` (ISO 8601 timestamp string).
- **Response**: Return modified or created records since `last_synced_at` alongside soft-deleted UUIDs.

```json
{
  "server_timestamp": "2026-07-22T19:25:00Z",
  "changes": {
    "lessons": [...],
    "assignments": [...],
    "submissions": [...]
  },
  "deletions": {
    "notes": ["uuid-1", "uuid-2"]
  }
}
```

### Push Endpoint (`/api/v1/sync/push`)
- Receives payload of offline client modifications.
- **Conflict Resolution Rules**:
  - **Submission/Quiz**: Client offline submission timestamp preserved; last-write-wins if duplicate attempt.
  - **Notes**: Merge content if updated after server copy; fallback to highest version number.
  - Returns sync confirmation and updated server timestamps for client state reconciliation.

---

## 3. Real-Time WebSockets (Laravel Reverb)

- Use Laravel Reverb for lightweight, high-performance WebSockets.
- Broadcast key events:
  - `AssignmentSubmitted`
  - `SyncCompleted`
  - `EssayEvaluated`
  - `LiveNotificationBroadcast`
- Private channel authorization based on Sanctum authenticated user IDs (`private-user.{id}`).

---

## 4. Database & Performance Setup

- Database: PostgreSQL with `pgvector` enabled (`CREATE EXTENSION IF NOT EXISTS vector;`).
- Application Server: Laravel Octane / FrankenPHP for high concurrency and zero-downtime execution.
