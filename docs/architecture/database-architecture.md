# Database Architecture 

## Introduction 

The database layer is responsible for persistent application data. 

The application accesses the database through models and dedicated persistence logic. 

The general principle: 

````
Database access belongs to the backend persistence layer, not to controllers or frontend code. 
````

--- 

# Database Flow

The typical data flow is: 

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

# Database Responsibilities

The database stores: 

- users 
- games 
- game states 
- configurations 
- rulesets 
- relationships 

--- 

# Database Access Layer

The application uses models as the database abstraction layer. 

Example: 

````
GameController

    |

    v

GameService

    |

    v

GameModel

    |

    v

Database
````

--- 

# Models 

Models represent persistent entities. 

Examples: 

````
UserModel 

GameModel 

GameRuleSetModel 

GameStateModel 
````

--- 

# Model Responsibilities 

Models are responsible for: 

- loading entities 
- storing entities 
- updating persistence state 
- database queries 

--- 

# Model Design Principles

Models should: 

- represent data 
- encapsulate persistence logic 
- provide entity-related operations 

--- 

# Models Should Avoid 

Models should not: 

- render views 
- process HTTP requests 
- manage sessions 
- contain complete application workflows 

--- 

# Entity Lifecycle

A typical entity lifecycle: 

````
Create

 |

 v

Persist

 |

 v

Load

 |

 v

Modify

 |

 v

Persist Changes
````

Example: 

````
Create Game

    |

    v

GameModel::create()

    |

    v

Database INSERT

    |

    v

Game available
````

--- 

# Game Data Architecture

A game consists of multiple related data areas. 

Example: 

````
Game

 |
 +----------------+
 |                |
 v                v

Players        Ruleset


 |
 v

Game State
````

--- 

# Ruleset Storage

Game rules are stored separately from game state. 

This allows: 

- configurable games 
- reusable rulesets 
- different game variants 

Example: 

````
Game

    |

    v

GameRuleSet

    |

    v

Rules
````

--- 

# State Management 

Dynamic game state is separated from static game configuration. 

Example: 

Static: 

````
Game configuration

- name
- ruleset
- creator
````

Dynamic: 

````
Game state

- current player
- dice result
- positions
- progress
````

--- 

# Database Integrity 

Important rules: 

- relationships should remain consistent 
- invalid states should be prevented 
- business constraints should be checked before persistence 

--- 

# Transactions 

Operations involving multiple database changes should use transactions. 

Example: 

Joining a game: 

````
BEGIN TRANSACTION

    Add Player

    Update Player Count

    Update Game State

COMMIT
````

--- 

# Database Access Principles 

## Do 

- use models for persistence 
- keep queries centralized 
- validate business rules before writing 

--- 

## Avoid 

Do not: 

- query the database from views 
- access database directly from JavaScripts
- duplicate SQL logic in multiple locations 

--- 

# Future Extension Possibilities 

The architecture allows future additions: 

- repository layer 
- database migrations 
- query builders 
- caching layer 
- event-based persistence 

--- 

# Summary 

The database architecture provides: 

- controlled persistence 
- clear separation of concerns 
- maintainable data access 
- extensible entity handling 

The database remains an implementation detail behind the backend architecture. 
