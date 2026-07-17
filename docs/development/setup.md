# Development Setup

This document describes how to prepare a local development environment.

The goal is that every developer can run the complete application locally using Docker.

---

# Requirements

Install the following software:

## Required

- Git
- Docker
- Docker Compose


## Recommended

- Visual Studio Code
- Docker Extension
- PHP Extension
- JavaScript Extension


---

# Clone Repository

Clone the repository:

```bash
git clone TODO_REPOSITORY_URL
```

Enter the project directory:

```bash
cd TODO_PROJECT_NAME
```

---

# Project Structure

After cloning:

```
project/

├── docker-compose.yml

├── src/

├── public/

├── js/

├── css/

├── database/

└── docs/
```

---

# Environment Configuration

The application uses environment variables.

Create the local environment file:

```bash
cp .env.example .env
```

Configure:

```
APP_ENV=development

DATABASE_HOST=
DATABASE_NAME=
DATABASE_USER=
DATABASE_PASSWORD=
```

TODO:
Document project specific variables here.

---

# Docker Architecture

The development environment consists of multiple containers.

Example:

```
                  Browser

                     |
                     |

                Web Server

                     |
                     |

             PHP Application

                     |
                     |

                 Database


                     |

               phpMyAdmin
```

---

# Containers

## Application Container

Responsible for:

- PHP runtime
- backend execution
- application dependencies


Container name:

```
TODO
```

---

## Web Container

Responsible for:

- HTTP requests
- routing
- serving static files


Container name:

```
TODO
```

---

## Database Container

Responsible for:

- persistent data storage


Technology:

```
TODO
```

---

## phpMyAdmin Container

Provides database administration.

Access:

```
http://localhost:TODO_PORT
```

---

# Start Development Environment

Build containers:

```bash
docker compose build
```

Start containers:

```bash
docker compose up
```

Run in background:

```bash
docker compose up -d
```

---

# Stop Environment

Stop containers:

```bash
docker compose down
```

---

# Database Setup

After the first start:

Run migrations:

```bash
TODO
```

Import initial data:

```bash
TODO
```

---

# Access Application

Application:

```
http://localhost:TODO_PORT
```

phpMyAdmin:

```
http://localhost:TODO_PORT
```

---

# Development Workflow

## Backend Changes

Backend code:

```
src/
```

Depending on configuration, restart the application container:

```bash
docker compose restart app
```

---

## Frontend Changes

Frontend files:

```
public/js/
public/css/
```

Usually browser refresh is sufficient.

---

# Logs

Application logs:

```bash
docker compose logs -f app
```

Web server logs:

```bash
docker compose logs -f web
```

Database logs:

```bash
docker compose logs -f db
```

---

# IDE Configuration

Recommended settings:

## PHP

Enable:

- PHP language support
- code formatting
- static analysis


## JavaScript

Enable:

- ES support
- formatting
- debugging


## Docker

Install:

- Docker extension

Useful for:

- container status
- logs
- shell access

---

# Debugging

## Backend

Check:

```
docker compose logs
```

---

## Database

Access through:

phpMyAdmin

or:

```bash
docker compose exec db bash
```

---

# Adding New Features

Before implementing new functionality:

1. Identify the correct architectural layer

Example:

```
Controller
    |
    Service
    |
    Model
```

2. Check reusable frontend systems

Examples:

- Binding System
- Shared JavaScript utilities
- Viewer components

3. Update documentation if a new architectural concept is introduced.

---

# Documentation Guidelines

Documentation belongs into:

```
docs/
```

Important changes should include updates to:

- architecture documentation
- feature documentation
- setup documentation

---

# Troubleshooting

## Containers do not start

Check:

```bash
docker compose logs
```

---

## Database connection failed

Verify:

- container is running
- environment variables
- database credentials

---

## Frontend changes not visible

Try:

- browser hard reload
- clear cache
- verify served files

---

# Final Notes

A working local environment should allow every developer to:

- start all containers
- access the application
- access the database
- modify backend and frontend code
- test new features locally

--- 

# Old Stuff

Install and start Docker Environment
1. docker compose up -d --build
2. docker compose exec app composer install

Stop Docker Environment
1. docker compose down