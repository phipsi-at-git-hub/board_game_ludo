# ludo_board_game

## Overview 

This project is a web-based game platform with a modular backend architecture, declarative frontend binding system and interactive Three.js based visualization.

---

# Overview

This project provides a complete environment for managing and playing configurable games.

Main capabilities:

- game creation and management
- player participation
- permission-based actions
- configurable rulesets
- dynamic frontend updates without page reloads
- interactive 3D visualization with THREE.js
- extensible game architecture


The application is designed with a strong separation between:

- backend business logic
- frontend presentation
- API communication
- game visualization

---

# Architecture

The project follows a layered architecture:

```
                 Browser
                    |
                    |
              JavaScript Layer
                    |
        -------------------------
        |                       |
 Binding System            Viewer
        |
        |
        API / JSON
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

---

# Backend Architecture

The backend follows an MVC based structure with a dedicated service layer.

Responsibilities:

## Controllers

Controllers are responsible for:

- receiving requests
- validating input
- authentication checks
- calling services
- returning responses


Controllers should not contain business logic.

---

## Services

Services contain application logic.

Examples:

- creating games
- joining games
- permission checks
- state transitions


The service layer is the central location for business rules.

---

## Models

Models represent:

- persisted entities
- database interaction
- entity state


---

# Frontend Architecture

The frontend consists of multiple independent systems.

```
public/

js/

├── app/
│   Application specific logic
│
├── viewer/
│   Three.js visualization
│
└── shared/
    Reusable frontend utilities
```

---

# JSON Binding System

The application uses a declarative JSON binding system.

Instead of writing custom JavaScript for every interaction, HTML elements define how they react to API responses.

Example:

```html
<span
    data-bind-1-dto-key="player_count"
    data-bind-1-type="text">
</span>
```

The backend returns:

```json
{
    "data": {
        "player_count": 3
    }
}
```

The element updates automatically.

Documentation:

```
docs/frontend/binding-system.md
```

---

# Viewer

The viewer is responsible for:

- Three.js initialization
- scene management
- rendering
- game visualization


The viewer is separated from the application logic.

Documentation:

```
docs/frontend/viewer.md
```

---

# Project Structure

Example structure:

```
project/

├── src/
│
│   Backend application
│
├── public/
│
│   Public web resources
│
├── js/
│
│   Frontend JavaScript
│
├── css/
│
│   Stylesheets
│
├── database/
│
│   Database scripts
│
├── docker/
│
│   Docker configuration
│
├── docs/
│
│   Developer documentation
│
├── docker-compose.yml
│
└── README.md
```

---

# Documentation

Developer documentation:

```
docs/
```

Important documents:

| Document | Description |
|-|-|
| docs/development/setup.md | Local development setup |
| docs/frontend/binding-system.md | JSON Binding System |
| docs/frontend/viewer.md | Three.js Viewer |
| docs/architecture/overview.md | Architecture overview |

---

# Technology Stack

## Backend

TODO:

- PHP version
- Framework / custom MVC
- ORM or database layer


## Frontend

- JavaScript
- HTML
- CSS
- Three.js


## Infrastructure

- Docker
- Docker Compose
- Database container
- Application container
- Web server container
- phpMyAdmin

---

# Development

For setting up the project locally:

See:

```
docs/development/setup.md
```

The setup guide covers:

- required software
- Docker installation
- environment configuration
- database setup
- starting containers
- IDE configuration

---

# Development Principles

The project follows these principles:

## Keep business logic centralized

Business decisions belong into services.

---

## Keep frontend generic

Frontend systems should provide reusable mechanisms.

Avoid:

- endpoint specific JavaScript
- duplicated UI logic
- business decisions in JavaScript


---

## Prefer declarative systems

Whenever possible:

Define behavior through:

- HTML attributes
- DTO contracts
- reusable components

instead of:

- manually manipulating DOM elements
- custom scripts for every feature

---

# License

TODO

