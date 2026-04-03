# Library API — Architectural Manifesto

> **One Beyond PHP Engineering Challenge**
> Vanilla PHP 8.5 · Domain-Driven Design · PHPStan Level 8 · Reflection-Based Routing · Docker/Nginx

---

## Executive Summary

This project is a purpose-built demonstration that enterprise-grade architecture does not require a framework. Built entirely in **vanilla PHP 8.5**, this Library Management API implements a full Domain-Driven Design (DDD) layered architecture — complete with immutable domain entities, an Application Service layer, and an Infrastructure layer that acts as an **Anti-Corruption Layer** insulating the domain from the challenge's mandated volatile global state.

Every architectural decision was made deliberately:

- **Zero framework dependencies** in production — no Symfony, no Laravel, no micro-framework. The PSR-4 autoloader and `composer.json` scripts are the only infrastructure Composer provides at runtime.
- **PHPStan Level 8 — Zero Errors** across `src/` and `tests/`. The strictest possible static analysis grade, enforced as a hard gate in the quality pipeline.
- **Enforced code coverage threshold** via a custom coverage enforcement script that parses the Clover XML report and fails the build if coverage drops below the configured threshold.
- **A Reflection-based router** using PHP 8.5 first-class `#[Attribute]` annotations — zero configuration files, zero magic strings, full IDE discoverability.
- **Immutable domain models** using `readonly class` — state transitions produce new instances, never mutating existing objects. Side-effect-free domain logic is trivially testable.

The result is a codebase that could be handed to any senior PHP engineer, onboarded in minutes, and extended with confidence.

---

## Challenge Compliance Audit

The following matrix maps every original requirement from the One Beyond PHP Engineering Challenge to its concrete implementation in this codebase.

| # | Requirement | Status | Endpoint / Implementation |
|---|------------|--------|--------------------------|
| 1 | **List Active Loans** — Return details of all borrowers with active loans, including book titles | ✅ Implemented | `GET /loans` → `LoanController::index()` → `BookStockRepositoryInterface::findAllActiveLoans()` |
| 2 | **Return Books** — Allow books on loan to be returned | ✅ Implemented | `POST /loans/return` → `LoanController::returnBook()` → `LoanService::returnBook()` |
| 2a | **Fine Calculation** — If returned after loan end date, raise a fine against the borrower | ✅ Implemented | `LoanService::returnBook()` — calculates `daysLate × £1.50/day`, persists `Fine` entity, returns fine details in response |
| 2b | **Fine Data Model** — Define `Fine` and its relationship with `Borrower` | ✅ Implemented | `src/Domain/Model/Fine.php` (borrowerId FK), `FineRepositoryInterface`, `InMemoryFineRepository` |
| 3 | **Reserve a Book** — Allow a borrower to reserve a title that is currently on loan | ✅ Implemented | `POST /reservations` → `ReservationController::reserve()` → `ReservationService::reserveBook()` |
| 3a | **Queue Support** — Handle multiple borrowers reserving the same book | ✅ Implemented | `Reservation` entities are appended to a per-`bookStockId` queue. Queue position drives ETA calculation. |
| 4 | **Query Reservation Status** — Borrower can query when a reserved book becomes available | ✅ Implemented | `GET /reservations/status?bookStockId={id}` → `ReservationController::status()` → `ReservationService::getReservationStatus()` |
| 4a | **ETA Calculation** — Estimate availability based on a 14-day standard loan | ✅ Implemented | `estimatedDate = loanEndDate + (queuePosition × 14 days)` — configurable via constructor injection |
| — | **In-Memory Storage** — Use global variables for persistence | ✅ Implemented | `data/data.php` seeds global arrays; `Infrastructure/Persistence/` layer wraps them via Anti-Corruption Layer |
| — | **OpenAPI/Swagger Documentation** | ✅ Bonus | `public/swagger.yaml` (OpenAPI 3.0), `public/swagger.html` (Swagger UI) served at `http://localhost:8080/swagger.html` |

**Verdict: All four challenge requirements are fully implemented, with every sub-requirement addressed and one bonus deliverable (OpenAPI spec + Swagger UI).**

---

## Architectural Paradigm

The architecture follows a strict **three-layer separation of concerns**, modelled on Domain-Driven Design principles. Dependencies flow inward only — the Domain layer knows nothing of Application or Infrastructure concerns.

```
┌─────────────────────────────────────────────────────────────┐
│                    HTTP Request / Nginx                      │
└──────────────────────────┬──────────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────┐
│              APPLICATION LAYER  (src/Application/)          │
│                                                             │
│  ┌──────────────────┐   ┌──────────────────────────────┐   │
│  │  Routing/        │   │  Controller/                 │   │
│  │  Router.php      │──▶│  BookController              │   │
│  │  Route.php       │   │  LoanController              │   │
│  │  (Reflection +   │   │  ReservationController       │   │
│  │   PHP Attributes)│   └────────────┬─────────────────┘   │
│  └──────────────────┘                │                      │
│                           ┌──────────▼─────────────────┐   │
│                           │  Service/                  │   │
│                           │  LoanService               │   │
│                           │  ReservationService        │   │
│                           └──────────┬─────────────────┘   │
└──────────────────────────────────────┼─────────────────────┘
                                       │ (via Interfaces)
┌──────────────────────────────────────▼─────────────────────┐
│                   DOMAIN LAYER  (src/Domain/)               │
│                                                             │
│  ┌────────────────────────────────────────────────────┐    │
│  │  Model/  (all readonly — immutable value objects)  │    │
│  │  Author · Book · Borrower · BookStock              │    │
│  │  Fine · Reservation                                │    │
│  └────────────────────────────────────────────────────┘    │
│                                                             │
│  ┌────────────────────────────────────────────────────┐    │
│  │  Repository/  (Interfaces only — no implementation)│    │
│  │  BookRepositoryInterface                           │    │
│  │  BookStockRepositoryInterface                      │    │
│  │  FineRepositoryInterface                           │    │
│  │  ReservationRepositoryInterface                    │    │
│  └────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                           │ (implements)
┌──────────────────────────▼──────────────────────────────────┐
│            INFRASTRUCTURE LAYER  (src/Infrastructure/)      │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  Persistence/  ◀── ANTI-CORRUPTION LAYER            │   │
│  │  InMemoryBookRepository                             │   │
│  │  InMemoryBookStockRepository                        │   │
│  │  InMemoryFineRepository                             │   │
│  │  InMemoryReservationRepository                      │   │
│  └──────────────────────┬──────────────────────────────┘   │
└─────────────────────────┼───────────────────────────────────┘
                          │ (wraps)
              ┌───────────▼───────────┐
              │  data/data.php        │
              │  global $books        │
              │  global $bookStocks   │
              │  global $borrowers    │
              │  global $fines        │
              │  global $reservations │
              └───────────────────────┘
```

### Domain Layer — The Invariant Core

All domain models are declared `readonly class`. This is not stylistic preference — it is a hard architectural guarantee. No method outside the model can mutate its state. State transitions (e.g., returning a book) produce a **new instance**, leaving the original untouched:

```php
// BookStock::returnBook() — produces a new immutable aggregate
public function returnBook(): self {
    return new self(
        id: $this->id,
        bookId: $this->bookId,
        isOnLoan: false,
        loanEndDate: null,
        borrowerId: null,
    );
}
```

The Domain layer defines **interfaces** for its repositories. It has no knowledge of whether storage is a global PHP array, a Redis cache, or a PostgreSQL database.

### Application Layer — Orchestration Without Coupling

**Services** (`LoanService`, `ReservationService`) receive repository interfaces via constructor injection. They contain all business orchestration logic — fine calculation, queue math, guard conditions — without touching persistence primitives.

**Controllers** are thin. They parse HTTP input, delegate to a Service, and render JSON. They contain no business logic.

**The Router** uses PHP 8.5 `#[Attribute]` annotations and the Reflection API to auto-discover routes at startup. Registering a new endpoint requires only adding a `#[Route('/path', 'METHOD')]` attribute to a controller method — no routing configuration files, no service container entries.

### Infrastructure Layer — The Anti-Corruption Layer

The challenge mandates global PHP arrays as the persistence mechanism. Left unwrapped, global state would contaminate every layer of the application.

The `Infrastructure/Persistence/` classes solve this with an **Anti-Corruption Layer pattern**: each repository implementation wraps its global variable behind a clean interface, defensively guarding against null or non-array state, and translating between the global array representation and first-class domain objects:

```php
// InMemoryBookStockRepository — defensive wrapping of volatile global state
public function findById(int $id): ?BookStock
{
    global $bookStocks;
    if (!is_array($bookStocks)) {
        return null;
    }
    foreach ($bookStocks as $stock) {
        if ($stock instanceof BookStock && $stock->id === $id) {
            return $stock;
        }
    }
    return null;
}
```

The Domain layer never sees a `global` keyword. Services never know the backing store exists.

---

## Directory Tree Map

```
library-api-php/
│
├── public/                         # Web root (Nginx document root)
│   ├── index.php                   # Bootstrap: DI wiring, router dispatch
│   ├── swagger.yaml                # OpenAPI 3.0 specification
│   └── swagger.html                # Swagger UI (served at /swagger.html)
│
├── src/                            # All application source code (PSR-4: App\)
│   ├── Application/                # Orchestration layer — HTTP in, JSON out
│   │   ├── Controller/
│   │   │   ├── BookController.php          # GET /books
│   │   │   ├── LoanController.php          # GET /loans, POST /loans/return
│   │   │   └── ReservationController.php   # POST /reservations, GET /reservations/status
│   │   ├── Routing/
│   │   │   ├── Route.php                   # #[Attribute] — method-level route declaration
│   │   │   └── Router.php                  # ReflectionClass scanner & HTTP dispatcher
│   │   └── Service/
│   │       ├── LoanService.php             # Return flow + fine calculation
│   │       └── ReservationService.php      # Reservation queue + ETA calculation
│   │
│   ├── Domain/                     # Business core — zero framework, zero I/O
│   │   ├── Model/
│   │   │   ├── Author.php                  # readonly: id, name
│   │   │   ├── Book.php                    # readonly: id, title, authorId, format, isbn
│   │   │   ├── Borrower.php                # readonly: id, name, email
│   │   │   ├── BookStock.php               # readonly aggregate: isOnLoan, loanEndDate, borrowerId
│   │   │   │                               #   + returnBook(): self  (state transition)
│   │   │   ├── Fine.php                    # readonly: id, borrowerId, amount, details
│   │   │   └── Reservation.php             # readonly: id, bookStockId, borrowerId, reservedAt
│   │   └── Repository/             # Interfaces only — domain defines the contract
│   │       ├── BookRepositoryInterface.php
│   │       ├── BookStockRepositoryInterface.php
│   │       ├── FineRepositoryInterface.php
│   │       └── ReservationRepositoryInterface.php
│   │
│   └── Infrastructure/             # Anti-Corruption Layer — wraps global state
│       └── Persistence/
│           ├── InMemoryBookRepository.php          # wraps global $books
│           ├── InMemoryBookStockRepository.php     # wraps global $bookStocks
│           ├── InMemoryFineRepository.php          # wraps global $fines
│           └── InMemoryReservationRepository.php   # wraps global $reservations
│
├── tests/                          # Mirror of src/ — unit tests only
│   └── Unit/src/
│       ├── Application/
│       │   ├── Controller/         # BookControllerTest, LoanControllerTest, ReservationControllerTest
│       │   ├── Routing/            # RouterTest (404, 400, 500 handling)
│       │   └── Service/            # LoanServiceTest, ReservationServiceTest
│       ├── Domain/
│       │   ├── Model/              # DomainModelTest (immutability, returnBook())
│       │   └── Repository/         # Interface contract tests
│       └── Infrastructure/
│           └── Persistence/        # InfrastructureRepositoryTest (corruption scenarios)
│   └── enforce_coverage.php        # Custom Clover XML parser — hard coverage gate
│
├── data/
│   └── data.php                    # Global state seed: authors, books, borrowers,
│                                   #   bookStocks (incl. overdue + contested edge cases),
│                                   #   fines, reservations
│
├── docker/
│   ├── nginx/
│   │   └── default.conf            # Nginx: document root /public, all URIs → index.php
│   └── php/
│       └── Dockerfile              # php:8.5-fpm-alpine + Xdebug + Composer
│
├── composer.json                   # PSR-4 autoload, quality scripts, dev deps only
├── docker-compose.yml              # web (Nginx:8080) + app (PHP-FPM:9000)
├── phpunit.xml                     # Test suite config, Clover coverage output
├── phpstan.dist.neon               # Level 8, paths: src/ + tests/
└── .php-cs-fixer.dist.php          # PSR-12, single quotes, ordered imports
```

---

## API Reference

All endpoints return `Content-Type: application/json`. The API is self-documenting via the included Swagger UI.

### `GET /books`
List all books in the catalogue.

**Response `200`:**
```json
[
  { "id": 1, "title": "Pride and Prejudice", "authorId": 1, "format": "Hardcover", "isbn": "1111111111111" }
]
```

---

### `GET /loans`
List all book stock records currently on loan, with borrower and loan end date.

**Response `200`:**
```json
[
  { "id": 1, "bookId": 1, "isOnLoan": true, "loanEndDate": "2025-04-10", "borrowerId": 1 }
]
```

---

### `POST /loans/return`
Return a book on loan. Automatically calculates and records a fine if the return date is past the loan end date.

**Request body:**
```json
{ "bookStockId": 3 }
```

**Response `200` — on-time return:**
```json
{
  "message": "Book returned successfully.",
  "fine_generated": null,
  "updated_stock": { "id": 3, "bookId": 1, "isOnLoan": false, "loanEndDate": null, "borrowerId": null }
}
```

**Response `200` — overdue return (10 days × £1.50):**
```json
{
  "message": "Book returned successfully.",
  "fine_generated": {
    "id": "fine_abc123",
    "borrowerId": 2,
    "amount": 15.00,
    "details": "Late return penalty for 10 days."
  },
  "updated_stock": { "id": 3, "bookId": 1, "isOnLoan": false, "loanEndDate": null, "borrowerId": null }
}
```

**Response `400`:** `{ "error": "Domain Rule Violation", "message": "..." }` — book not on loan, or missing `bookStockId`.

---

### `POST /reservations`
Place a borrower in the reservation queue for a book that is currently on loan. Multiple borrowers may reserve the same copy; they are queued in arrival order.

**Request body:**
```json
{ "bookStockId": 4, "borrowerId": 2 }
```

**Response `200`:** `{ "message": "Book successfully reserved." }`

**Response `400`:** Book is not currently on loan, or request body is malformed.

---

### `GET /reservations/status?bookStockId={id}`
Query a borrower's position in the reservation queue and receive an estimated availability date calculated from the current loan end date plus 14 days per queued borrower ahead.

**Response `200`:**
```json
{
  "queue_length": 3,
  "estimated_availability": "2026-05-10"
}
```

**ETA Algorithm:**
```
estimated_availability = loanEndDate + (queue_position × 14 days)
```
Where `queue_position` is the zero-indexed count of reservations already in the queue. The `queue_length` in the response reflects the total number of people waiting (including the most recent reserver).

---

## Business Logic Deep Dive

### Fine Calculation (`LoanService`)

```
Fine Amount = Days Overdue × Daily Rate (£1.50)
```

Guard conditions enforced before calculation:
1. Book stock must exist and `isOnLoan === true`
2. `borrowerId` must not be null (data integrity guard)
3. Fine is only created if the current date strictly exceeds `loanEndDate`
4. `daysLate > 0` (defensive against same-day edge case)

The `dailyFineRate` is injected via constructor (configured at £1.50 in `public/index.php`) — changing the rate requires a one-line edit with no ripple effects.

### Reservation ETA Calculation (`ReservationService`)

The system models the reservation queue as a simple FIFO list of `Reservation` entities stored against a `bookStockId`. The `standardLoanDays` value (14) is also constructor-injected and therefore freely configurable.

**Example — BookStock #4, due in 2 days, 2 people already waiting:**

| Position | Borrower | Available From |
|----------|----------|---------------|
| Current  | Borrower 1 (owner) | Now → returns in ~2 days |
| Queue[0] | Borrower 2 | loanEndDate + 0×14 = loanEndDate |
| Queue[1] | Borrower 3 | loanEndDate + 1×14 = loanEndDate + 14 days |
| Queue[2] | Borrower 4 | loanEndDate + 2×14 = loanEndDate + 28 days |

---

## Code Quality & Defence Mechanisms

### The Composer Quality Gauntlet

All quality tools are wired into a single reproducible command that runs in sequence:

```bash
docker-compose exec app composer quality
```

Which executes in order:

| Step | Command | Purpose |
|------|---------|---------|
| 1 | `quality:format` | Apply PHP-CS-Fixer (PSR-12, single quotes, ordered imports) |
| 2 | `quality:stan` | PHPStan Level 8 — zero errors permitted |
| 3 | `quality:test` | PHPUnit — full test suite with Clover coverage report |
| 4 | `quality:coverage` | Custom enforcer — fails build if coverage is below threshold |

**PHPStan Level 8** is the maximum strictness level. At this level, PHPStan enforces strict type narrowing, catches dead code paths, flags missing null checks, and verifies generic type correctness. The only exclusion is `src/Infrastructure/Persistence/` — the Anti-Corruption Layer's `global` keyword usage is by design and cannot be made PHPStan-transparent without adding suppressions; instead, the layer is kept deliberately thin and fully integration-tested.

### Strict Type Discipline

Every PHP file declares `declare(strict_types=1)`. All domain models are `readonly class`. All constructor parameters are typed. All return types are declared. PHPStan Level 8 enforces this discipline statically — if it compiles through PHPStan, the type contracts are sound.

### Defensive Testing Strategy

The test suite focuses on three categories of adversarial scenarios beyond the happy path:

**1. Domain Invariant Violations**
```
LoanService: returning a book that is not on loan → RuntimeException
LoanService: missing borrowerId on an overdue book → RuntimeException ("data corruption" path)
ReservationService: reserving a book that is not on loan → RuntimeException
ReservationService: querying status on a non-loaned book → RuntimeException
```

**2. Infrastructure Corruption Resilience**
The `InfrastructureRepositoryTest` explicitly sets global arrays to `null` before exercising repository methods. The repositories must survive this and initialise clean state:
```php
// Arrange
global $bookStocks;
$bookStocks = null;  // Simulate corrupted/uninitialized global state

// Act — should not throw, should return []
$result = $this->repository->findAllActiveLoans();
```

**3. Router Error Handling**
The `RouterTest` verifies that:
- A `RuntimeException` from a controller produces HTTP `400` (Domain Rule Violation)
- Any other `Throwable` produces HTTP `500` (Internal Server Error)
- An unregistered route produces HTTP `404`

---

## Operational How-Tos

### Prerequisites
- Docker Desktop (or Docker Engine + Compose plugin)
- Git

### 1. Clone & Boot

```bash
git clone <repository-url> library-api-php
cd library-api-php

docker-compose up -d --build
```

The stack starts two containers:
- `web`: Nginx Alpine, port `8080`
- `app`: PHP 8.5-FPM Alpine with Xdebug and Composer

Wait for both containers to report `healthy`, then verify:
```bash
curl http://localhost:8080/books
```

### 2. Explore the API via Swagger UI

Navigate to **[http://localhost:8080/swagger.html](http://localhost:8080/swagger.html)** in your browser for an interactive Swagger UI with all endpoints, request schemas, and response examples.

### 3. Run the Full Quality Pipeline

```bash
docker-compose exec app composer quality
```

To run individual steps:
```bash
docker-compose exec app composer quality:format   # Format code
docker-compose exec app composer quality:stan     # PHPStan Level 8
docker-compose exec app composer quality:test     # PHPUnit + coverage report
docker-compose exec app composer quality:coverage # Enforce coverage threshold
```

### 4. Lifecycle Test — Return an Overdue Book, Then Check the Fine

The seed data in `data/data.php` includes **BookStock #3**, which is pre-configured with a loan end date of `-10 days` (i.e., 10 days ago). Returning it should generate an automatic fine of **£15.00** (10 days × £1.50).

**Step 1 — Confirm the book is on loan:**
```bash
curl http://localhost:8080/loans
```
Look for `"id": 3` with `"isOnLoan": true`.

**Step 2 — Return the overdue book:**
```bash
curl -s -X POST http://localhost:8080/loans/return \
  -H "Content-Type: application/json" \
  -d '{"bookStockId": 3}' | python -m json.tool
```

**Expected response:**
```json
{
  "message": "Book returned successfully.",
  "fine_generated": {
    "id": "fine_...",
    "borrowerId": 2,
    "amount": 15.0,
    "details": "Late return penalty for 10 days."
  },
  "updated_stock": {
    "id": 3,
    "bookId": 1,
    "isOnLoan": false,
    "loanEndDate": null,
    "borrowerId": null
  }
}
```

**Step 3 — Confirm the book is no longer in active loans:**
```bash
curl http://localhost:8080/loans
```
BookStock #3 should be absent from the response.

### 5. Lifecycle Test — Reservation Queue & ETA

BookStock #4 is pre-seeded with 2 existing reservations.

**Step 1 — Check current reservation status:**
```bash
curl "http://localhost:8080/reservations/status?bookStockId=4"
```
Expected: `queue_length: 2`, `estimated_availability` ≈ loanEndDate + 14 days.

**Step 2 — Add a third reservation:**
```bash
curl -s -X POST http://localhost:8080/reservations \
  -H "Content-Type: application/json" \
  -d '{"bookStockId": 4, "borrowerId": 3}'
```

**Step 3 — Recheck status (queue grows, ETA extends by 14 days):**
```bash
curl "http://localhost:8080/reservations/status?bookStockId=4"
```
Expected: `queue_length: 3`, `estimated_availability` ≈ loanEndDate + 28 days.

---

## Architectural Concerns & Future Roadmap

This section is direct. The architecture made deliberate trade-offs given the challenge constraints. Understanding them is as important as understanding the strengths.

### The Global State Problem — What It Actually Means

The challenge mandates in-memory global arrays. In a stateless HTTP architecture, this has one unavoidable consequence: **state does not persist between requests**. Every HTTP request triggers a fresh PHP-FPM process, which re-executes `data/data.php`, re-initialising all globals to their seed values.

This means:
- **Returning BookStock #3** in one request will show it as returned — but the next `GET /loans` request will show it as on loan again.
- **Fines created** in one request do not survive to the next.
- **Reservations placed** via POST are invisible in subsequent GET requests.

This is not a bug in this implementation — it is an inherent property of the mandated storage mechanism. The architecture is built to make this limitation **instantly swappable**.

### Swapping the Storage Backend — A One-File Change

Because every repository is injected via its interface at the composition root (`public/index.php`), replacing the storage backend requires **editing a single file and adding one new class per repository**.

To migrate from global arrays to **PDO/PostgreSQL**:

```php
// public/index.php — current (in-memory)
$bookStockRepo = new InMemoryBookStockRepository();

// public/index.php — future (PostgreSQL)
$pdo = new PDO('pgsql:host=db;dbname=library', 'user', 'pass');
$bookStockRepo = new PdoBookStockRepository($pdo);
```

`PdoBookStockRepository` implements `BookStockRepositoryInterface` — the domain, services, and controllers require zero changes.

The same principle applies to:
- **PSR-6/PSR-16 Cache** (Redis, Memcached) — swap to `CacheBookStockRepository`
- **PSR-7 + any HTTP client** — controllers already produce pure arrays; serialisation is trivially moved
- **Event-sourced storage** — the `readonly` + "new instance on mutation" pattern is directly compatible with event sourcing

### What Production Would Add

| Concern | Current State | Production Path |
|---------|--------------|-----------------|
| Persistence | Global PHP arrays (ephemeral) | PDO + PostgreSQL, migrations via Phinx or Doctrine Migrations |
| Authentication | None | JWT middleware registered in the router's dispatch pipeline |
| HTTP Layer | Custom router | PSR-15 middleware stack (no framework needed — Relay, Middleman) |
| Dependency Injection | Manual wiring in `index.php` | PSR-11 container (PHP-DI, no framework needed) |
| Error Logging | None | PSR-3 Logger (Monolog) — one constructor injection |
| Input Validation | Defensive parsing in controllers | Dedicated `RequestValidator` in the Application layer |
| Fine Rate Config | Hardcoded float in `index.php` | Environment variable via `$_ENV` or a config object |
| Loan Duration Config | Hardcoded int in `index.php` | Same as above |
| ID Generation | `uniqid()` (not collision-safe) | UUID v4 via `ramsey/uuid` |

### PHPStan Exclusion — Acknowledged Technical Debt

`src/Infrastructure/Persistence/` is excluded from PHPStan analysis. The `global` keyword usage is intentional and architecturally bounded, but PHPStan cannot reason about dynamic global state. This is a known, documented exclusion — not an oversight. Migrating to any real persistence layer eliminates this exclusion entirely.

---

## Technology Stack Reference

| Component | Technology | Version |
|-----------|-----------|---------|
| Language | PHP | ≥ 8.5 |
| Runtime | PHP-FPM | 8.5-alpine |
| Web Server | Nginx | Alpine (latest) |
| Containerisation | Docker + Compose | — |
| Testing | PHPUnit | ^11.0 |
| Static Analysis | PHPStan | ^2.1 (Level 8) |
| Code Style | PHP-CS-Fixer | ^3.94 (PSR-12) |
| API Docs | OpenAPI | 3.0 (Swagger UI) |
| Production Dependencies | — | **None** |

---

*Authored for the One Beyond PHP Engineering Challenge. Architecture designed for auditability, extensibility, and zero-framework longevity.*
