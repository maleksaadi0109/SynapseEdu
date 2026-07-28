# System Design Specification: EduBridge AI

**Project Name**: EduBridge AI  
**Architecture Type**: Offline-First Distributed Systems + AI RAG Engine  
**Backend Framework**: Laravel 13 (FrankenPHP / Octane)  
**Database**: PostgreSQL 16 with `pgvector` & Redis  
**Mobile Client**: Flutter (Offline SQLite / Drift Local Database)

---

## 1. Executive Summary

**EduBridge AI** is an offline-first educational platform designed for region-resilient learning. Students can study, complete assignments, take quizzes, and write notes entirely offline on their mobile devices. Whenever network connectivity is detected, a background delta synchronization engine securely exchanges data updates with the central Laravel API server while resolving data conflicts.

Furthermore, the platform integrates **Retrieval-Augmented Generation (RAG)** for AI tutoring and an **AI Essay Evaluation engine** backed by **Fleiss' Kappa** inter-rater statistical validation.

---

## 2. High-Level System Architecture

The following diagram illustrates the complete system architecture and boundary separation between the mobile app, offline local storage, central API gateway, real-time WebSocket layer, AI vector search engine, and background task processing.

```mermaid
graph TB
    subgraph Mobile_Client["Mobile Application (Flutter Client)"]
        UI["Flutter UI Layer (Dashboards, Reader, Quiz, Chat)"]
        State["State Management (Riverpod / Bloc)"]
        LocalDB[("Local SQLite Database (Drift / sqflite)")]
        SecureStore["Flutter Secure Storage (Auth Tokens)"]
        SyncManager["Mobile Background Sync Manager (WorkManager)"]

        UI --> State
        State <--> LocalDB
        SyncManager <--> LocalDB
        SyncManager --> SecureStore
    end

    subgraph Edge_Gateway["API & Real-Time Gateway"]
        Nginx["FrankenPHP / Octane Web Server (Port 8000)"]
        Reverb["Laravel Reverb WebSockets (Port 8080)"]
    end

    subgraph Backend_App["Laravel 13 API Core"]
        AuthService["Auth & Sanctum Service"]
        SyncEngine["Delta Sync Service (Pull / Push)"]
        CourseService["Course & Assignment Service"]
        RAGService["Laravel AI SDK & RAG Service"]
        KappaService["Fleiss' Kappa Validation Engine"]
    end

    subgraph Infrastructure["Data & AI Infrastructure"]
        Postgres[("PostgreSQL 16 + pgvector")]
        Redis[("Redis (Cache, Sessions & Event Broker)")]
        QueueWorker["Background Task Queue Worker"]
        AI_LLM["External LLM Provider (Gemini / OpenAI API)"]
    end

    SyncManager -- "REST API (HTTPS/JSON)" --> Nginx
    State -- "WebSockets (WSS)" --> Reverb

    Nginx --> AuthService
    Nginx --> SyncEngine
    Nginx --> CourseService
    Nginx --> RAGService
    Nginx --> KappaService

    SyncEngine <--> Postgres
    SyncEngine <--> Redis
    RAGService <--> Postgres
    RAGService <--> AI_LLM
    KappaService <--> Postgres
    QueueWorker <--> Redis
    QueueWorker <--> Postgres
```

---

## 3. Offline Synchronization Protocol (Push / Pull)

The synchronization engine uses a **timestamp-based incremental delta protocol** to ensure low bandwidth consumption and reliable offline state recovery.

### 3.1 Push / Pull Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    participant App as Mobile App (SQLite)
    participant Sync as Sync Manager
    participant API as Laravel Sync API (/api/v1/sync)
    participant DB as PostgreSQL DB
    participant WS as Reverb WebSockets

    Note over App, Sync: 1. User performs offline actions (Submissions, Notes)
    App->>App: Save record locally in SQLite (pending_sync = true)

    Note over Sync: 2. Network connection detected
    Sync->>API: POST /api/v1/sync/push (Payload: Pending Local Changes)
    API->>DB: Process changes & resolve conflicts (Last-Write-Wins / Merge)
    DB-->>API: Conflict Resolution Success
    API-->>Sync: Return HTTP 200 (Synced UUIDs & Server Timestamps)
    Sync->>App: Mark local items as synced (pending_sync = false)

    Note over Sync: 3. Pull latest changes from server
    Sync->>API: GET /api/v1/sync/pull?last_synced_at=2026-07-22T20:00:00Z
    API->>DB: Query records updated/deleted after last_synced_at
    DB-->>API: Return modified entities & soft-deleted UUIDs
    API-->>Sync: Return Delta JSON payload
    Sync->>App: Apply updates to local SQLite inside single Transaction
    Sync->>WS: Subscribe to user channel for live updates
```

---

## 4. AI RAG & Essay Evaluation Architecture

### 4.1 Retrieval-Augmented Generation (RAG) Flow

```mermaid
flowchart LR
    A["Educational Material (PDFs/Lessons)"] --> B["Document Chunking (500-token chunks)"]
    B --> C["Laravel AI SDK Vector Embeddings"]
    C --> D[("PostgreSQL pgvector Table")]

    E["Student AI Question"] --> F["Generate Vector Embedding for Query"]
    F --> G["Cosine Distance Similarity Search (pgvector)"]
    D --> G
    G --> H["Inject Retrieved Context into System Prompt"]
    H --> I["LLM Provider (Gemini / OpenAI)"]
    I --> J["Verified AI Tutor Answer (No Hallucinations)"]
```

### 4.2 Fleiss' Kappa Statistical Agreement Engine

To validate AI essay evaluations against human teacher grading:

1. Ratings are categorized into $K$ discrete grade bands (e.g., _Excellent_, _Good_, _Average_, _Poor_).
2. Human teacher grades ($r_1, r_2$) and AI scores ($r_{\text{ai}}$) form an evaluation matrix for $N$ essays.
3. The system computes **Fleiss' Kappa** ($\kappa$):
   $$\kappa = \frac{\bar{P} - \bar{P}_e}{1 - \bar{P}_e}$$
4. If $\kappa \ge 0.75$, AI grading is certified as highly reliable. If $\kappa < 0.40$, the system flags prompts for recalibration.

---

## 5. Database Schema & Data Models (PostgreSQL & SQLite)

### 5.1 Entity Relationship Overview

```mermaid
erDiagram
    USERS ||--o{ SUBMISSIONS : submits
    USERS ||--o{ NOTES : creates
    COURSES ||--o{ LESSONS : contains
    LESSONS ||--o{ DOCUMENT_CHUNKS : vector_indexes
    LESSONS ||--o{ ASSIGNMENTS : includes
    ASSIGNMENTS ||--o{ SUBMISSIONS : receives
    SUBMISSIONS ||--o{ ESSAY_EVALUATIONS : evaluated_by

    USERS {
        uuid id PK
        string name
        string email
        string role "student | teacher | admin"
        timestamp created_at
        timestamp updated_at
    }

    COURSES {
        uuid id PK
        uuid teacher_id FK
        string title
        text description
        timestamp updated_at
        timestamp deleted_at
    }

    LESSONS {
        uuid id PK
        uuid course_id FK
        string title
        text content
        timestamp updated_at
        timestamp deleted_at
    }

    DOCUMENT_CHUNKS {
        uuid id PK
        uuid lesson_id FK
        text chunk_content
        vector embedding "1536-dim vector"
    }

    ASSIGNMENTS {
        uuid id PK
        uuid lesson_id FK
        string title
        text instructions
        timestamp due_date
        timestamp updated_at
    }

    SUBMISSIONS {
        uuid id PK
        uuid assignment_id FK
        uuid student_id FK
        text content
        timestamp submitted_at
        timestamp updated_at
    }

    ESSAY_EVALUATIONS {
        uuid id PK
        uuid submission_id FK
        integer ai_score
        jsonb rubric_breakdown
        text ai_feedback
        integer human_score
        float kappa_score
    }

    NOTES {
        uuid id PK
        uuid user_id FK
        string title
        text body
        integer version
        timestamp updated_at
        timestamp deleted_at
    }
```

---

## 6. Security & Real-Time Communication

- **Authentication**: Laravel Sanctum bearer tokens stored securely in `flutter_secure_storage` on mobile.
- **WebSocket Security**: Private channels authenticated via Sanctum (`private-user.{userId}`).
- **Data Protection**: Local SQLite database encrypted using SQLCipher on mobile devices.
