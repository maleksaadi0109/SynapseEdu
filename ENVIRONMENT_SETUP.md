# EduBridge AI - Containerized Environment Setup Guide

This document outlines the complete environment architecture, configuration files, and step-by-step instructions for running the **EduBridge AI** backend using Docker, PostgreSQL with `pgvector`, Redis, Laravel Reverb (WebSockets), and Adminer.

---

## 1. System Architecture Overview

The development environment consists of 6 containerized services running under Docker Compose:

1. **`app` (Laravel API Server)**:
   - **Base Image**: `dunglas/frankenphp:latest` (PHP 8.4 + FrankenPHP web server).
   - **Port**: `8000` (`http://localhost:8000`).
   - **Volume Sync**: Real-time code synchronization (`- .:/app:z`) with SELinux support.

2. **`postgres` (Database + AI Vectors)**:
   - **Image**: `pgvector/pgvector:pg16` (PostgreSQL 16 with native vector search).
   - **Port**: `5432`.
   - **Auto-Initialization**: Loads `docker/postgres/init-vector.sql` on startup to enable `CREATE EXTENSION IF NOT EXISTS vector;`.

3. **`redis` (Cache, Sessions & Queue Broker)**:
   - **Image**: `redis:7-alpine`.
   - **Port**: `6379`.

4. **`adminer` (Web Database GUI Manager)**:
   - **Image**: `adminer:latest`.
   - **Port**: `8081` (`http://localhost:8081`).

5. **`reverb` (Real-Time WebSockets)**:
   - **Port**: `8080` (Laravel Reverb WebSocket server).

6. **`queue` (Background Worker)**:
   - Executes background jobs (AI essay processing, data synchronization tasks).

---

## 2. Configured Files Reference

| File | Location | Description |
| :--- | :--- | :--- |
| **`Dockerfile`** | [Dockerfile](file:///home/supersusi/myprojects/education/backend-api/Dockerfile) | Configures PHP 8.4, PostgreSQL extensions (`pdo_pgsql`), Redis, and Composer. |
| **`docker-compose.yml`** | [docker-compose.yml](file:///home/supersusi/myprojects/education/backend-api/docker-compose.yml) | Orchestrates all 6 services with SELinux volume bindings (`:z`). |
| **`init-vector.sql`** | [docker/postgres/init-vector.sql](file:///home/supersusi/myprojects/education/backend-api/docker/postgres/init-vector.sql) | Enables the `pgvector` PostgreSQL extension on database initialization. |
| **`.env`** | [.env](file:///home/supersusi/myprojects/education/backend-api/.env) | Configures database credentials, Redis connection, and Reverb WebSocket keys. |

---

## 3. Daily Command Guide

### Start the Environment
```bash
sudo docker-compose up -d
```

### Stop the Environment
```bash
sudo docker-compose down
```

### Run Composer Commands Inside Container
```bash
sudo docker-compose exec app composer install
```

### Run Database Migrations
```bash
sudo docker-compose exec app php artisan migrate
```

### Create New Migrations
```bash
sudo docker-compose exec app php artisan make:migration create_example_table
```

### Verify PostgreSQL `pgvector` Extension
```bash
sudo docker-compose exec postgres psql -U edubridge -d edubridge -c "\dx"
```

---

## 4. How to Access Services

- 🌐 **Laravel REST API Server**: [http://localhost:8000](http://localhost:8000)
- 🗄️ **Adminer Database Web Manager**: [http://localhost:8081](http://localhost:8081)
  - **System**: `PostgreSQL`
  - **Server**: `postgres`
  - **Username**: `edubridge`
  - **Password**: `secret`
  - **Database**: `edubridge`

---

## 5. Custom Workspace Skills Installed

The workspace is equipped with 3 project-specific skills in `.agents/skills/`:
1. [laravel-backend-sync](file:///home/supersusi/myprojects/education/backend-api/.agents/skills/laravel-backend-sync/SKILL.md) (REST API & Sync Engine)
2. [flutter-offline-sync](file:///home/supersusi/myprojects/education/backend-api/.agents/skills/flutter-offline-sync/SKILL.md) (Mobile App & SQLite Sync)
3. [ai-rag-essay-eval](file:///home/supersusi/myprojects/education/backend-api/.agents/skills/ai-rag-essay-eval/SKILL.md) (RAG Vector Search & Fleiss' Kappa)

---

## 6. Linux & SELinux Troubleshooting Notes

- **Fedora SELinux Permissions**:
  Volume mounts in `docker-compose.yml` use `- .:/app:z` so SELinux allows Docker to read local project files.
- **Run Without `sudo`**:
  To execute Docker commands without typing `sudo`, run:
  `sudo usermod -aG docker $USER` (then log out and log back in).
