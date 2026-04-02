# Bionic Backend — Docker Deployment Guide

A CodeIgniter 4 API backend for the Cleaning Tracker application.
Stack: **PHP 8.3 + Apache**, **PostgreSQL 16**.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Quick Start (docker compose)](#quick-start)
3. [Environment Variables Reference](#environment-variables-reference)
4. [Build & Push to Docker Hub](#build--push-to-docker-hub)
5. [Pull & Run on a Server](#pull--run-on-a-server)
6. [Database Seeding](#database-seeding)
7. [Useful Commands](#useful-commands)
8. [Troubleshooting](#troubleshooting)

---

## Prerequisites

| Tool           | Minimum version |
| -------------- | --------------- |
| Docker         | 24              |
| Docker Compose | v2 (plugin)     |

---

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/muhammad-fahmi/bionic-backend.git
cd bionic-backend

# 2. Create your .env from the example
cp .env.example .env
# Edit .env and set at least DB_PASS to something secure

# 3. Build and start all services
docker compose up -d --build

# 4. Check logs
docker compose logs -f app
```

The API is now available at **http://localhost:8080** (or the `APP_PORT` you set).  
Migrations run automatically on every container start.

---

## Environment Variables Reference

All variables are defined in `.env` (copy from `.env.example`).

| Variable               | Default                                  | Description                                            |
| ---------------------- | ---------------------------------------- | ------------------------------------------------------ |
| `DOCKER_IMAGE`         | `your-dockerhub-username/bionic-backend` | Image name used for build/push                         |
| `DOCKER_TAG`           | `latest`                                 | Image tag                                              |
| `APP_PORT`             | `8080`                                   | Host port mapped to container port 80                  |
| `CI_ENVIRONMENT`       | `production`                             | CodeIgniter environment (`production` / `development`) |
| `APP_BASE_URL`         | `http://localhost:8080/`                 | Full base URL of the API                               |
| `DB_NAME`              | `bionic_db`                              | PostgreSQL database name                               |
| `DB_USER`              | `bionic_user`                            | PostgreSQL username                                    |
| `DB_PASS`              | `secret`                                 | PostgreSQL password — **change this**                  |
| `CACHE_HANDLER`        | `redis`                                  | Primary cache handler                                  |
| `CACHE_BACKUP_HANDLER` | `file`                                   | Fallback cache handler                                 |
| `REDIS_PORT`           | `6379`                                   | Redis service port                                     |
| `REDIS_PASSWORD`       | (empty)                                  | Redis password (if enabled)                            |
| `REDIS_DB`             | `0`                                      | Redis logical database index                           |
| `AUTO_SEED`            | `true`                                   | Run seeding check on container startup                 |
| `SEED_FORCE`           | `false`                                  | Force seeding even when data already exists            |
| `SEEDER_CLASS`         | `MainSeeder`                             | Seeder class used when seeding runs                    |

> `DB_HOST`, `DB_DRIVER`, and `REDIS_HOST` are fixed to container service names inside `docker-compose.yml`.

---

## Build & Push to Docker Hub

### 1. Log in to Docker Hub

```bash
docker login
```

### 2. Set your image name in `.env`

```dotenv
DOCKER_IMAGE=yourusername/bionic-backend
DOCKER_TAG=1.0.0
```

### 3. Build the image

```bash
# Using docker compose (reads DOCKER_IMAGE and DOCKER_TAG from .env)
docker compose build app

# Or with plain docker
docker build -t yourusername/bionic-backend:1.0.0 .
```

### 4. Push to Docker Hub

```bash
# Using docker compose
docker compose push app

# Or with plain docker
docker push yourusername/bionic-backend:1.0.0
```

### 5. (Optional) Tag as latest

```bash
docker tag yourusername/bionic-backend:1.0.0 yourusername/bionic-backend:latest
docker push yourusername/bionic-backend:latest
```

---

## Pull & Run on a Server

On your production server you only need `docker-compose.yml` and a `.env` file — no source code required.

### Option A — copy only the compose file

```bash
# On the server
mkdir bionic && cd bionic

# Download only what you need (adjust URL to match your repo)
curl -O https://raw.githubusercontent.com/muhammad-fahmi/bionic-backend/master/docker-compose.yml
curl -O https://raw.githubusercontent.com/muhammad-fahmi/bionic-backend/master/.env.example
cp .env.example .env

# Edit .env
nano .env   # Set DOCKER_IMAGE, DB_PASS, APP_BASE_URL, etc.

# Pull the pre-built image and start
docker compose pull
docker compose up -d
```

### Option B — clone the full repository and run

```bash
git clone https://github.com/muhammad-fahmi/bionic-backend.git
cd bionic-backend
cp .env.example .env
nano .env
docker compose up -d --build
```

The container automatically:

1. Writes `/var/www/html/.env` from the environment variables.
2. Waits for PostgreSQL to become healthy.
3. Runs `php spark migrate --all -f`.
4. Runs seeding (`SEEDER_CLASS`) when the database is empty (or when `SEED_FORCE=true`).
5. Starts Apache.

---

## Database Seeding

Seeders populate initial master data (locations, items, actions, users).
The seed data is read from `datasource.xlsx` which is bundled inside the image.

```bash
# Run all seeders through the running container
docker compose exec app php spark db:seed MainSeeder

# Or individual seeders
docker compose exec app php spark db:seed UserSeeder
docker compose exec app php spark db:seed LocationSeeder
docker compose exec app php spark db:seed ItemSeeder
docker compose exec app php spark db:seed ActionSeeder
```

---

## Useful Commands

```bash
# View live logs
docker compose logs -f app

# Open a shell inside the app container
docker compose exec app bash

# Run a Spark CLI command
docker compose exec app php spark <command>

# Restart only the app (e.g., after a config change)
docker compose restart app

# Stop everything (data is preserved in volumes)
docker compose down

# Stop and REMOVE all volumes (⚠ destroys the database)
docker compose down -v

# Rebuild the image after a code change
docker compose up -d --build
```

---

## Troubleshooting

### App keeps restarting

Check logs with `docker compose logs app`. Most common cause: the database health-check failed. Make sure `DB_PASS` in `.env` matches on both the `app` and `db` services.

### 500 errors / "Unable to connect to database"

Verify the environment variables inside the container:

```bash
docker compose exec app env | grep DB_
```

### Migrations fail

Ensure the database is healthy first:

```bash
docker compose ps db
```

Then re-run manually:

```bash
docker compose exec app php spark migrate --all -f
```

### Permission errors on `writable/`

```bash
docker compose exec app chown -R www-data:www-data /var/www/html/writable
```
