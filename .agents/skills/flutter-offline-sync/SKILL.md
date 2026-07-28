---
name: flutter-offline-sync
description: >-
  Provides architectural rules, local storage patterns (SQLite/Drift), background synchronization 
  routines, and offline UI state management instructions for the EduBridge AI Flutter mobile app.
---

# Flutter Offline-First & Background Sync Architecture Skill

Use this skill when developing, refactoring, or testing Flutter components and mobile synchronization logic for EduBridge AI.

---

## 1. Offline-First Architecture Principles

1. **Local-First Writes**:
   - Every user action (completing quizzes, writing notes, submitting assignments) MUST be written to local SQLite storage first.
   - The app must remain 100% operational offline without blocking UI state on network calls.

2. **Sync State Queue**:
   - Maintain a local `pending_sync_queue` table tracking local changes:
     - `entity_type` (e.g. 'submission', 'note')
     - `entity_id` (UUID)
     - `action` ('CREATE', 'UPDATE', 'DELETE')
     - `payload` (JSON string)
     - `status` ('PENDING', 'IN_PROGRESS', 'FAILED')
     - `attempts` (Integer)

---

## 2. Background Sync Engine

- Use WorkManager / background service handlers to trigger background sync when connectivity is restored.
- **Sync Routine Flow**:
  1. Check network connectivity status.
  2. Perform **Push**: Read `pending_sync_queue`, send POST batch payload to `/api/v1/sync/push`.
  3. Mark synced queue items as completed/deleted on success.
  4. Perform **Pull**: Send GET to `/api/v1/sync/pull?last_synced_at={timestamp}`.
  5. Apply server updates and soft deletions to local SQLite tables inside a transaction.
  6. Update local `last_synced_at` timestamp.

---

## 3. Local Database & State Management

- Use **Drift** (or `sqflite`) for SQLite type-safe query management.
- Store sensitive tokens (JWT/Sanctum bearer tokens) in Flutter Secure Storage (`flutter_secure_storage`).
- Use `StateNotifier` / `Riverpod` or `Bloc` to reactively render local database changes to the UI using reactive Streams.
