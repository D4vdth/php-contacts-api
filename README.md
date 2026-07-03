# PHP Contacts API

REST API for contact management built with **PHP 8.5** 


## Tech Stack

| Layer | Technology |
|-------|-----------|
| Language | PHP 8.5 (CLI, no framework) |
| Database | SQLite 3 (PDO, WAL mode) |
| Containerization | Docker + Docker Compose |
| Autoloading | Composer (PSR-4) |
| Testing | PHPUnit 13 |

## Architecture

The project follows Clean Architecture with three layers. Dependencies always point inward — Infrastructure depends on Application, Application depends on Domain, never the reverse.

```
src/
├── Domain/              # Entities, Value Objects, repository interfaces, exceptions
│   ├── Entity/          # Contact (aggregate root)
│   ├── ValueObject/     # Email, Phone, Name, LastName, Uuid
│   ├── Repository/      # ContactRepositoryInterface, PaginatedResult
│   └── Exception/       # Domain-specific exceptions
│
├── Application/         # Use Cases and DTOs
│   ├── UseCase/         # CreateContact, GetContact, UpdateContact, DeleteContact, ListContacts
│   └── DTO/             # CreateContactDto, UpdateContactDto, ListContactsDto
│
└── Infrastructure/      # HTTP, persistence, configuration
    ├── Http/            # Request, Response, Router, Controller
    ├── Persistence/     # SQLite connection, migrations, repository implementation
    └── Config/          # DI Container
```

## Getting Started

### Prerequisites

- Docker and Docker Compose installed

### Setup

```bash
git clone <repository-url>
cd php-contacts-api
docker compose up -d
```

The container automatically installs Composer dependencies on first run. The API is available at `http://localhost:8000`.


## API Reference

**Base URL:** `http://localhost:8000/api/v1`

All request and response bodies use `Content-Type: application/json`.

---

### List Contacts

```
GET /api/v1/contacts
```

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | `1` | Page number (min: 1) |
| `per_page` | integer | `15` | Items per page (min: 1, max: 100) |
| `sort` | string | `created_at` | Sort field: `name`, `last_name`, `email`, `created_at`, `updated_at` |
| `order` | string | `desc` | Sort direction: `asc`, `desc` |
| `name` | string | — | Filter by name (partial match) |
| `email` | string | — | Filter by email (partial match) |

**Response:** `200 OK`

```json
{
    "data": [
        {
            "id": "a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d",
            "name": "User",
            "lastName": "Test",
            "email": "test@example.com",
            "phones": ["+573001234567"],
            "createdAt": "2026-07-03T02:17:03+00:00",
            "updatedAt": "2026-07-03T02:17:03+00:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 1,
        "total_pages": 1
    }
}
```

---

### Get Contact

```
GET /api/v1/contacts/{id}
```

**Response:** `200 OK`

```json
{
    "id": "a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d",
    "name": "User",
    "lastName": "Test",
    "email": "test@example.com",
    "phones": ["+573001234567"],
    "createdAt": "2026-07-03T02:17:03+00:00",
    "updatedAt": "2026-07-03T02:17:03+00:00"
}
```

**Errors:** `404` Contact not found

---

### Create Contact

```
POST /api/v1/contacts
```

**Request Body:**

```json
{
    "name": "User",
    "last_name": "Test",
    "email": "test@example.com",
    "phones": ["+573001234567", "+573009876543"]
}
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `name` | string | Yes | 2-100 characters, letters only |
| `last_name` | string | Yes | 2-100 characters, letters only |
| `email` | string | Yes | Valid email, unique in the system |
| `phones` | string[] | No | E.164 format (`+` followed by 7-15 digits), no duplicates |

**Response:** `201 Created`

**Errors:** `409` Email already exists · `422` Validation error

---

### Update Contact

```
PUT /api/v1/contacts/{id}
```

**Request Body:** Same structure as Create.

**Response:** `200 OK`

**Errors:** `404` Contact not found · `409` Email already exists · `422` Validation error

---

### Delete Contact

```
DELETE /api/v1/contacts/{id}
```

**Response:** `204 No Content`

**Errors:** `404` Contact not found

---

## Error Responses

All errors follow a consistent format:

```json
{
    "error": "Contact \"abc-123\" not found."
}
```

Validation errors:

```json
{
    "errors": {
        "message": "The value \"bad\" is not a valid email address."
    }
}
```

| Status | Meaning |
|--------|---------|
| `404` | Resource not found |
| `409` | Conflict (duplicate email) |
| `422` | Validation error (invalid data) |
| `500` | Internal server error |

## Examples

```bash
# Create a contact
curl -s -X POST http://localhost:8000/api/v1/contacts \
  -H "Content-Type: application/json" \
  -d '{"name":"María","last_name":"García","email":"maria@example.com","phones":["+573001234567"]}' | jq

# List with pagination and filters
curl -s "http://localhost:8000/api/v1/contacts?page=1&per_page=5&sort=name&order=asc&name=mar" | jq

# Update a contact
curl -s -X PUT http://localhost:8000/api/v1/contacts/{id} \
  -H "Content-Type: application/json" \
  -d '{"name":"María Alejandra","last_name":"García López","email":"maria.new@example.com","phones":["+573009876543"]}' | jq

# Delete a contact
curl -X DELETE http://localhost:8000/api/v1/contacts/{id}
``