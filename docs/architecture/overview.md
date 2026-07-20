# Architecture Overview

## Introduction

This document provides a high-level overview of the application architecture.

The project is a web-based game platform designed around a modular backend architecture, a reusable frontend system and an extensible game engine.

The main architectural goals are:

- clear separation of responsibilities
- maintainable business logic
- reusable application components
- controlled dependency management
- extensibility for future game types and features

---

# High-Level Architecture

The application consists of several major layers:

```
                    Browser
                       |
                       |
              Frontend Application
                       |
        --------------------------------
        |                              |
   Binding System                  Viewer
        |
        |
              HTTP / JSON API
                       |
                       |
                Controllers
                       |
                       |
                  Services
                       |
                       |
                   Models
                       |
                       |
                  Database
```

# Architectural Principles

## Separation of Responsibilities 

Each application layer has a clearly defined responsibility. 

Business decisions should exist only in dedicated backend layers. 

Example: 

- Controllers handle requests.
- Services implement application logic. 
- Models represent persisted data. 
- Frontend system handle presentation. 

---

# Service-Oriented Backend

The application uses a dedicated service layer. 

Services are responsible for: 

- workflows
- business rules
- complex operations
- coordination between models

Examples: 

````
GameService
SystemService
````

Controllers should not contain complex business logic. 

---

# Dependency Management 

Application dependencies are managed through an application container. 

The container provides: 

- controlled object creation 
- request lifetime management 
- singleton services 
- centralized dependency resolution 

The application lifecycle is managed by: 

```
App
 |
 v
Container
 |
 v
Services
```

Detailed documentation: 

```
docs/architecture/application-lifecycle.md
docs/architecture/dependency-container.md
```

---

# Backend Structure

The backend follows a layered MVC-inspired architecture: 
```
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

Responsibilities: 

## Controllers

Controllers are responsible for: 

- receiving HTTP requests
- validating input 
- authentication checks 
- returning responses 

Controllers should remain lightweight. 

---

## Services

Services contain application logic. 

Examples: 

- creating games
- joining games
- processing game actions
- checking system configuration 

---

## Models

Models represent: 

- persisted entities 
- database interaction 
- stored state 

--- 

# Frontend Architecture

The frontend is separated into independent systems. 

````
public/

js/

├── app/
│
│   Application logic
│
├── shared/
│
│   Reusable utilities
│
└── viewer/
│
    Three.js visualization
````

The frontend follows the principle: 

````
Generic frontend systems should be reusable and driven by data.
````

---

# Viewer Architecture

The viewer is responsible for: 

- THREE.js initialization 
- scene handling 
- rendering 
- visual representation of game state 

The viewer does not contain game rules. 

Game logic remains in the backend. 

--- 

# Database Architecture 

The database layer is accessed through models. 

The application avoids direct database access from: 

- controllers 
- views
- frontend code 

The typical flow is: 

```
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

# Request Lifecycle

Each HTTP request creates one application context. 

The lifecycle: 

````
HTTP Request

    |
    v

index.php

    |
    v

App

    |
    v

Container

    |
    v

Router

    |
    v

Controller

    |
    v

Service / Model
````

The application context exists only for the current request. 

--- 

# Documentation Structure

Detailed architecture documentation: 

````
docs/architecture/

├── overview.md
│
├── application-lifecycle.md
│
├── dependency-container.md
│
├── backend-layer.md
│
├── frontend-architecture.md
│
└── database-architecture.md
````

--- 

# Design Goals

Future development should follow these principles: 

## Keep business logic centralized

Business rules belong into services. 

--- 

## Avoid duplicated mechanisms

Application-wide functionality should have one responsible component. 

Examples: 

- configuration handling 
- service lifecycle 
- authentication 
- localization 

--- 

## Prefer explicit architecture 

New features should follow existing patterns: 

````
Route
 |
Controller
 |
Service
 |
Model
 |
Database
````

---

# Summary

The application is structures as a modular layered system. 

The architecture provides: 

- separation of concerns 
- predictable request lifecycle 
- centralized dependency management 
- reusable frontend systems 
- extensible backend design 

The goal is not only current functionality, but long-term maintainability. 
