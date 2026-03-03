# Modèle Conceptuel de Données (MCD) - EcoRide

Ce document présente la structure de la base de données relationnelle et non-relationnelle d'EcoRide.

## Base de données Relationnelle (MySQL / MariaDB)

```mermaid
erDiagram
    USERS {
        int id PK
        string email
        string password
        string pseudo
        string first_name
        string last_name
        string phone
        string role "admin, employe, utilisateur"
        int credits
        datetime created_at
    }

    VEHICLES {
        int id PK
        int user_id FK
        string registration
        string model
        string color
        boolean is_electric
        datetime date_first_registration
    }

    TRIPS {
        int id PK
        int driver_id FK
        int vehicle_id FK
        string departure_city
        string destination_city
        date departure_date
        time departure_time
        decimal price
        int max_duration
        int max_seats
        string status "planned, started, finished, cancelled"
    }

    RESERVATIONS {
        int id PK
        int trip_id FK
        int passenger_id FK
        string status "pending, confirmed, cancelled"
        datetime created_at
    }

    USERS ||--o{ VEHICLES : possède
    USERS ||--o{ TRIPS : conduit
    USERS ||--o{ RESERVATIONS : reserve
    VEHICLES ||--o{ TRIPS : "est utilisé pour"
    TRIPS ||--o{ RESERVATIONS : "contient"
```

## Base de données Non-relationnelle (MongoDB)

```mermaid
erDiagram
    REVIEWS {
        ObjectId _id PK
        int trip_id
        int reviewer_id
        int driver_id
        int rating
        string comment
        string status "pending, approved, rejected"
        date created_at
    }
```
