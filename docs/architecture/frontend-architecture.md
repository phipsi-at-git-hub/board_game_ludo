# Frontend Architecture

## Introduction 

The frontend architecture is designed as a collection of independent and reusable systems. 

The frontend is separated from backend business logic and communicates primarily through HTTP requests and JSON responses. 

The main goals are: 

- reusable frontend components 
- minimal duplicated JavaScript logic 
- declarative UI behavior 
- separation between application logic and visualization 

--- 

# Frontend Structure

The frontend follows this genreal structure: 


````
public/

├── js/
│
│   ├── app/
│   │
│   │   Application specific frontend logic
│   │
│   ├── shared/
│   │
│   │   Reusable frontend utilities
│   │
│   └── viewer/
│
│       Three.js based visualization
│
├── css/
│
│   Stylesheets
│
└── assets/
    
    Static resources
````

--- 

# Frontend Systems

The frontend consists of several independent systems: 

````
Frontend

    |
    +----------------+
    |                |
    v                v

Application       Viewer

Logic             Visualization

    |
    v

Binding System
````

--- 

# Application Layer 

## Purpose 

The application layer contains frontend logic that belongs directly to the web application. 

Examples: 

- page initialization 
- user interactions 
- API communication 
- from handling 
- UI state handling 

--- 

# Responsibilities 

The application layer should: 

- communicate with backend endpoints 
- update application-specific UI 
- coordinate frontend components 

It should not: 

- implement backend business rules 
- duplicate server-side validation 
- contain game rule calculations 

--- 

# Shared Frontend Utilities 

The shared layer contains reusable functionality. 

Examples: 

````
shared/

├── api/
│
│   HTTP communication helpers
│
├── dom/
│
│   DOM utilities
│
└── components/

    Generic UI components
````

--- 

# Design Principle 

Reusable functionality belongs into shared systems. 

Avoid: 

````
game-specific code
        |
        v
copied into multiple pages
````

Prefer: 

````
Reusable component

        |

        v

Used by multiple features
````

--- 

# JSON Binding System 

## Overview 

The application uses a declarative JSON binding mechanism. 

Instead of manually writing JavaScript for every UI update, HTML elements describe how data should be applied. 

Example: 

````html
<span
    data-bind-1-dto-key="player_count"
    data-bind-1-type="text">
</span>
````

The backend provides: 

````json
{
    "player_count": 3
}
````

The binding system updates the element automatically. 

--- 

# Binding Philosophy 

The binding system follows the principle: 

````
The backend provides data. The frontend declares how data is displayed. 
````

This reduces: 

- duplicated JavaScript 
- endpoint-specific DOM manipulation 
- inconsistent UI updates 

--- 

# Binding Responsibilities

The binding system handles: 

- locating bound elements 
- reading DTO keys 
- applying values 
- updating the DOM 

It should not handle: 

- business decisions 
- permission logic 
- game rules 

--- 

# API Communication 

The frontend communicates with the backend through HTTP endpoints. 

Typical flow: 

````
User Action

    |

    v

JavaScript

    |

    v

API Request

    |

    v

Controller

    |

    v

Service

    |

    v

JSON Response

    |

    v

Frontend Update
````

--- 

# Viewer Architecture 

## Overview 

The viewer is responsible for the visual representation of the game. 

The viewer uses: 

- THREE.js 
- WebGL rendering 
- scene management 
- object handling 

--- 

# Viewer Responsibilities 

The viewer handles: 

- creating scenes 
- loading visual assets 
- positioning objects 
- rendering frames 
- updating visual state 

--- 

# Viewer Does Not Handle 

The viewer should not contain: 

- game rules 
- permission checks 
- authoritative game state 
- database interaction 

The backend remains the source of truth. 

--- 

# Viewer Structure 

Example: 

````
viewer/

├── core/

│   Three.js initialization

├── scene/

│   Scene objects

├── objects/

│   Game entities

├── animation/

│   Animations

└── loader/

    Asset loading
````

--- 

# Game State Flow

The game state follows: 

````
Database

    |

    v

Backend Models

    |

    v

Services

    |

    v

API Controller

    |

    v

JSON

    |

    v

Viewer
````

--- 

# Separation Between Game and Viewer 

The viewer visualizes. 

It does not decide. 

Example: 

Incorrect: 

```javascript
if(player.canMove()) {
    moveFigure();
}
```

Correct: 

```javascript
Backend:

allowed_moves = [...]

Frontend:

display allowed moves
```

--- 

# Frontend Development Principles

## Prefer Declarative Systems 

Define behavior through: 

- HTML attributes 
- JSON structure 
- configuration objects 

Avoid: 

- duplicated scripts 
- page-specific hacks 

--- 

## Keep Frontend Generic 

The frontend should provide mechanisms. 

The backend provides decisions. 

--- 

## Avoid Business Logic Duplication 

A rule implemented in PHP should not be reimplemented in JavaScript. 

--- 

# Summary 

The frontend architecture provides: 

- reusable systems 
- separation of visualization and logic 
- backend-driven state 
- maintainable JavaScript 
- scalable THREE.js integration 
