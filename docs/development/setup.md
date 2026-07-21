# Development Setup

## Introduction

This document describes how to set up the project for local development.

The project is designed to run in a containerized environment using Docker and Docker Compose.

The goal is to provide a reproducible development environment with minimal host system dependencies.

---

# Requirements

The following software must be installed:

## Required

- Docker
- Docker Compose

Verify installation:

```bash
docker --version
docker compose version
```

---

## Recommended

Development tools:

- Visual Studio Code
- PhpStorm
- Git
- Postman or Bruno
- DBeaver

---

# Project Structure

Expected structure:

```text
project/

├── bootstrap/
├── config/
├── database/
├── docker/
├── docs/
├── public/
├── src/
├── vendor/
├── .env
├── docker-compose.yml
└── README.md
```

---

# Environment Configuration

## Create Environment File

Copy the example environment file:

```bash
cp .env.example .env
```

If no example file exists:

```bash
touch .env
```

---

## Example Environment Variables

Example:

```env
APP_ENV=development

DB_HOST=db
DB_PORT=3306
DB_NAME=ludo
DB_USER=ludo
DB_PASSWORD=secret

TZ=Europe/Berlin
```

Adjust values according to your environment.

---

# Starting the Application

## Build Containers

Build all containers:

```bash
docker compose build
```

---

## Start Containers

Start the application:

```bash
docker compose up -d
```

---

## Verify Running Containers

Check container status:

```bash
docker compose ps
```

Example:

```text
NAME            STATUS
app             running
db              running
phpmyadmin      running
```

---

# Accessing the Application

After startup:

Application:

```text
http://localhost
```

phpMyAdmin:

```text
http://localhost:8080
```

Adjust ports according to your Docker configuration.

---

# Installing Dependencies

PHP dependencies are managed through Composer.

Install dependencies:

```bash
composer install
```

Update dependencies:

```bash
composer update
```

---

# Autoload Regeneration

After creating new classes:

```bash
composer dump-autoload
```

This regenerates Composer's autoload mappings.

---

# Database Setup

## Initial Database Creation

Create database manually or through container initialization scripts.

Example:

```sql
CREATE DATABASE ludo;
```

---

## Import Schema

Import project schema:

```bash
mysql -u root -p ludo < database/schema.sql
```

---

## Import Seed Data

Optional:

```bash
mysql -u root -p ludo < database/seeds.sql
```

---

# Database Access

## phpMyAdmin

Open:

```text
http://localhost:8080
```

Use credentials from `.env`.

---

## CLI Access

Enter database container:

```bash
docker exec -it db bash
```

Connect:

```bash
mysql -u root -p
```

---

# Application Lifecycle During Development

Request flow:

```text
Browser

    |

    v

public/index.php

    |

    v

bootstrap/app.php

    |

    v

Application Boot

    |

    v

Router

    |

    v

Controller

    |

    v

Service

    |

    v

Model

    |

    v

Database
```

---

# Development Environment

## Development Mode

Enable development mode:

```env
APP_ENV=development
```

Effects:

- detailed PHP errors
- debug information
- asset rebuilding
- development diagnostics

---

## Production Mode

```env
APP_ENV=production
```

Effects:

- errors hidden
- debugging disabled
- optimized runtime behavior

---

# Asset Handling

Assets are generated automatically during development.

Examples:

```text
CSS
JavaScript
Generated bundles
```

Depending on project configuration, assets may be rebuilt automatically during bootstrap.

---

# Localization

Localization files are loaded during application boot.

Location:

```text
resources/translations/
```

Example:

```text
resources/translations/en-us.php
resources/translations/de-de.php
```

---

# Debugging

## Application Debug

Development mode enables:

```php
Debug::start();
```

and

```php
Debug::render();
```

at the end of the request.

---

## PHP Error Reporting

Development mode enables:

```php
error_reporting(E_ALL);
```

and

```php
ini_set('display_errors', 1);
```

---

# Logging

Future logging should be centralized.

Recommended location:

```text
storage/logs/
```

Examples:

```text
application.log
error.log
game.log
```

---

# Working With Docker

## Stop Containers

```bash
docker compose down
```

---

## Restart Containers

```bash
docker compose restart
```

---

## Rebuild Containers

```bash
docker compose down
docker compose build
docker compose up -d
```

---

## View Logs

All containers:

```bash
docker compose logs
```

Specific container:

```bash
docker compose logs app
```

Live logs:

```bash
docker compose logs -f
```

---

# Running Commands Inside Containers

Example:

```bash
docker exec -it app bash
```

Inside container:

```bash
composer install
php artisan
php script.php
```

Adjust commands to project needs.

---

# Development Workflow

Typical workflow:

```text
Pull Changes

    |

    v

Update Dependencies

    |

    v

Start Containers

    |

    v

Develop Feature

    |

    v

Test Feature

    |

    v

Commit Changes
```

---

# Troubleshooting

## Composer Autoload Issues

Regenerate:

```bash
composer dump-autoload
```

---

## Container Not Starting

Inspect:

```bash
docker compose logs
```

---

## Database Connection Failure

Verify:

```env
DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASSWORD
```

Check whether database container is running.

---

## Missing Environment Variables

Verify:

```bash
cat .env
```

and ensure:

```env
APP_ENV
DB_HOST
DB_NAME
DB_USER
DB_PASSWORD
```

exist.

---

# Coding Standards

General principles:

- Controllers stay thin
- Business logic belongs to services
- Models handle persistence
- Policies handle permissions
- Views remain presentation-only

---

# Related Documentation

Architecture overview:

```text
docs/architecture/overview.md
```

Application lifecycle:

```text
docs/architecture/application-lifecycle.md
```

Dependency container:

```text
docs/architecture/dependency-container.md
```

Backend layers:

```text
docs/architecture/backend-layer.md
```

---

# Summary

A local development environment consists of:

- Docker containers
- Composer dependencies
- configured environment variables
- initialized database
- development mode enabled

Once configured, development should only require:

```bash
docker compose up -d
```

to start working on the application.

--- 

# Old Stuff

Install and start Docker Environment
1. docker compose up -d --build
2. docker compose exec app composer install

Stop Docker Environment
1. docker compose down
