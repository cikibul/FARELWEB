# Product Requirement Document (PRD)
## Backend RESTful API — Farel Transport (Rental & Tour Package Website)

---

**Version:** 3.0
**Last Updated:** 2026-07-24
**Status:** Active Development
**Target Frontend:** Vue.js 3 SPA (Inertia.js / Vue Router + Axios)
**Database Engine:** SQLite3 (WAL Mode)

---

## 1. Executive Summary & Objectives

### 1.1 Product Description

Sistem backend API untuk website rental mobil & paket tour **Farel Transport** — perusahaan penyewaan kendaraan dan paket wisata berbasis di Yogyakarta. API ini menyediakan katalog armada, manajemen paket tour, sistem booking/inquiry, testimoni, dan panel admin dengan dua level akses (`admin_web` dan `admin_tour`) yang akan dikonsumsi oleh Vue.js Single Page Application.

### 1.2 Core Business Goals

| # | Goal | Metric |
|---|------|--------|
| 1 | Vehicle catalog accessible via public API | Response < 200ms on GET endpoints |
| 2 | Tour package management with dedicated admin role | `admin_tour` can manage tours independently |
| 3 | Booking/inquiry via WhatsApp integration | 100% inquiry generates valid WA link |
| 4 | Single-file database deployment | SQLite3 file-based, zero external dependencies |
| 5 | Clean RBAC with two admin tiers | `admin_web` = full access, `admin_tour` = tour-only |

### 1.3 SQLite3 Architectural Constraints & Advantages

| Aspect | Detail |
|--------|--------|
| **Advantage** | Zero-configuration database, single-file deployment, portable |
| **Advantage** | WAL mode enables concurrent reads during writes |
| **Advantage** | JSON1 extension supports JSON column queries natively |
| **Constraint** | Single-writer (no concurrent write transactions) |
| **Constraint** | No native ENUM type — use CHECK constraints or string validation |
| **Constraint** | No ALTER COLUMN — use migration rebuild pattern for schema changes |
| **Recommendation** | Enable WAL mode for better read concurrency |
| **Recommendation** | Use `journal_mode=WAL` and `busy_timeout=5000` in config |

### 1.4 Key Backend Goals

- **Performance:** < 200ms for all public GET endpoints
- **Clean API Contract:** Consistent JSON structure with proper HTTP status codes
- **RBAC:** Laravel Sanctum + custom role middleware (`admin_web`, `admin_tour`)
- **SQLite3 Optimized:** JSON casts, WAL mode, proper indexing
- **Vue.js SPA Ready:** CORS configured, pagination metadata, eager loading

---

## 2. Tech Stack & System Architecture

### 2.1 Core Technology

| Component | Technology | Version |
|-----------|-----------|---------|
| Framework | Laravel | 13.x |
| Language | PHP | 8.3+ |
| Database | SQLite3 | 3.35+ (JSON1 extension) |
| Authentication | Laravel Sanctum | 4.x |
| Frontend Consumer | Vue.js 3 SPA | via Axios / Inertia |
| Storage | Local (`storage/app/public/`) | symlink to `public/storage/` |
| Build Tool | Vite | 8.x |
| Package Manager | Composer / npm | latest |

### 2.2 Architecture Pattern

```
┌──────────────────────────────────────────────────────┐
│                Vue.js 3 SPA (Frontend)                │
│           Axios + Vue Router / Inertia.js             │
└───────────────────────┬──────────────────────────────┘
                        │ HTTP/REST (JSON)
┌───────────────────────▼──────────────────────────────┐
│              Laravel 13 REST API Backend               │
│  ┌──────────────┐ ┌────────────┐ ┌────────────────┐  │
│  │ Controllers  │ │ Middleware │ │ API Resources   │  │
│  │ (Public/Admin)│ │ (Role/Auth)│ │ (JSON format)  │  │
│  └──────────────┘ └────────────┘ └────────────────┘  │
│  ┌──────────────┐ ┌────────────┐ ┌────────────────┐  │
│  │ Models       │ │ Services   │ │ Helpers         │  │
│  │ (Eloquent)   │ │ (Business) │ │ (WhatsApp/dll)  │  │
│  └──────────────┘ └────────────┘ └────────────────┘  │
└───────────────────────┬──────────────────────────────┘
                        │
┌───────────────────────▼──────────────────────────────┐
│  SQLite3 (database.sqlite)  │  Local Storage (media)  │
│  WAL Mode + JSON1 extension │  storage/app/public/    │
└─────────────────────────────────────────────────────┘
```

### 2.3 SQLite3 Configuration

**`config/database.php` — SQLite connection:**

```php
'sqlite' => [
    'driver' => 'sqlite',
    'url' => env('DB_URL'),
    'database' => env('DB_DATABASE', database_path('database.sqlite')),
    'prefix' => '',
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
    'busy_timeout' => 5000,
    'journal_mode' => 'WAL',
    'synchronous' => 'NORMAL',
],
```

### 2.4 Database Indexing Strategy (SQLite3)

| Table | Column(s) | Index Type | Rationale |
|-------|-----------|------------|-----------|
| `users` | `email` | UNIQUE | Login lookup |
| `vehicles` | `slug` | UNIQUE | Public detail page lookup |
| `vehicles` | `is_available, sort_order` | COMPOSITE | Public catalog listing |
| `vehicles` | `category` | INDEX | Category filter |
| `tour_packages` | `slug` | UNIQUE | Public detail page lookup |
| `tour_packages` | `is_active, sort_order` | COMPOSITE | Public listing |
| `testimonials` | `is_approved, sort_order` | COMPOSITE | Public listing |
| `bookings_inquiries` | `booking_type` | INDEX | Filter by vehicle/tour |
| `bookings_inquiries` | `status` | INDEX | Admin status filter |
| `bookings_inquiries` | `created_at` | INDEX | Date range queries |

### 2.5 Media Storage Strategy

```
storage/app/public/
├── vehicles/              ← Vehicle images
├── packages/              ← Tour package images
└── testimonials/          ← Customer photos
```

- **Driver:** Local filesystem (`public` disk)
- **Symlink:** `php artisan storage:link` creates `public/storage/` → `storage/app/public/`
- **Upload Limits:** Max 2MB per file, allowed: jpg, jpeg, png, webp
- **Naming:** `{timestamp}_{random}.{ext}` to prevent collisions

---

## 3. Database Schema & Data Models (SQLite3 Compatible)

### 3.1 Entity Relationship Diagram

```
┌──────────────────┐
│      users       │
│  (admin_web /    │
│   admin_tour)    │
└──────────────────┘

┌──────────────────┐     ┌───────────────────────┐
│    vehicles      │     │   bookings_inquiries   │
│                  │────>│  (booking_type=vehicle)│
└──────────────────┘ 1:M └───────────────────────┘

┌──────────────────┐     ┌───────────────────────┐
│  tour_packages   │────>│   bookings_inquiries   │
│                  │     │  (booking_type=tour)   │
└──────────────────┘     └───────────────────────┘

┌──────────────────┐
│  testimonials    │
└──────────────────┘
```

### 3.2 Table: `users`

```sql
CREATE TABLE users (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    name            TEXT NOT NULL,
    email           TEXT NOT NULL UNIQUE,
    email_verified_at TEXT NULL,
    password        TEXT NOT NULL,
    role            TEXT NOT NULL DEFAULT 'admin_tour'
                    CHECK(role IN ('admin_web', 'admin_tour')),
    remember_token  TEXT NULL,
    created_at      TEXT NULL,
    updated_at      TEXT NULL
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
```

**Eloquent Model Casts:**

```php
protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
];
```

### 3.3 Table: `vehicles`

```sql
CREATE TABLE vehicles (
    id                 INTEGER PRIMARY KEY AUTOINCREMENT,
    name               TEXT NOT NULL,
    slug               TEXT NOT NULL UNIQUE,
    category           TEXT NOT NULL,
    passenger_capacity INTEGER NOT NULL,
    transmission       TEXT NOT NULL CHECK(transmission IN ('Manual', 'Matic')),
    price_half_day     REAL NOT NULL,
    price_full_day     REAL NOT NULL,
    description        TEXT NULL,
    image              TEXT NOT NULL,
    badge              TEXT NULL,
    inclusions         TEXT NOT NULL DEFAULT '[]',  -- JSON string
    is_available       INTEGER NOT NULL DEFAULT 1,
    sort_order         INTEGER NOT NULL DEFAULT 0,
    created_at         TEXT NULL,
    updated_at         TEXT NULL
);

CREATE UNIQUE INDEX idx_vehicles_slug ON vehicles(slug);
CREATE INDEX idx_vehicles_category ON vehicles(category);
CREATE INDEX idx_vehicles_available_sort ON vehicles(is_available, sort_order);
```

**Eloquent Model Casts:**

```php
protected $casts = [
    'inclusions' => 'array',    // JSON string ↔ PHP array
    'is_available' => 'boolean',
    'price_half_day' => 'decimal:2',
    'price_full_day' => 'decimal:2',
];
```

**Sample JSON Response:**

```json
{
  "id": 1,
  "name": "Toyota Avanza",
  "slug": "toyota-avanza",
  "category": "MPV",
  "passenger_capacity": 6,
  "transmission": "Manual",
  "price_half_day": "300000.00",
  "price_full_day": "500000.00",
  "image_url": "http://localhost:8000/storage/vehicles/avanza.jpg",
  "badge": "Terpopuler",
  "inclusions": ["Driver", "AC", "BBM"],
  "is_available": true,
  "wa_url": "https://wa.me/6281234567890?text=Halo%2C%20saya%20tertarik%20dengan%20Toyota%20Avanza."
}
```

### 3.4 Table: `tour_packages`

```sql
CREATE TABLE tour_packages (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    name              TEXT NOT NULL,
    slug              TEXT NOT NULL UNIQUE,
    duration_label    TEXT NOT NULL,
    destinations      TEXT NOT NULL,
    itinerary         TEXT NULL,
    price_per_pax     REAL NULL,
    price_per_package REAL NULL,
    amenities         TEXT NOT NULL DEFAULT '[]',  -- JSON string
    image             TEXT NOT NULL,
    is_active         INTEGER NOT NULL DEFAULT 1,
    sort_order        INTEGER NOT NULL DEFAULT 0,
    created_at        TEXT NULL,
    updated_at        TEXT NULL
);

CREATE UNIQUE INDEX idx_tour_packages_slug ON tour_packages(slug);
CREATE INDEX idx_tour_packages_active_sort ON tour_packages(is_active, sort_order);
```

**Eloquent Model Casts:**

```php
protected $casts = [
    'amenities' => 'array',
    'is_active' => 'boolean',
    'price_per_pax' => 'decimal:2',
    'price_per_package' => 'decimal:2',
];
```

### 3.5 Table: `testimonials`

```sql
CREATE TABLE testimonials (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_name TEXT NOT NULL,
    rating        INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5),
    content       TEXT NOT NULL,
    photo         TEXT NULL,
    is_approved   INTEGER NOT NULL DEFAULT 1,
    sort_order    INTEGER NOT NULL DEFAULT 0,
    created_at    TEXT NULL,
    updated_at    TEXT NULL
);

CREATE INDEX idx_testimonials_approved_sort ON testimonials(is_approved, sort_order);
```

**Eloquent Model Casts:**

```php
protected $casts = [
    'is_approved' => 'boolean',
];
```

### 3.6 Table: `bookings_inquiries`

```sql
CREATE TABLE bookings_inquiries (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_name       TEXT NOT NULL,
    phone_number        TEXT NOT NULL,
    package_or_vehicle_id INTEGER NULL,
    booking_type        TEXT NOT NULL CHECK(booking_type IN ('vehicle', 'tour')),
    booking_date        TEXT NOT NULL,   -- ISO 8601 date
    notes               TEXT NULL,
    status              TEXT NOT NULL DEFAULT 'new'
                        CHECK(status IN ('new', 'contacted', 'confirmed', 'completed', 'cancelled')),
    created_at          TEXT NULL,
    updated_at          TEXT NULL,

    FOREIGN KEY (package_or_vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    FOREIGN KEY (package_or_vehicle_id) REFERENCES tour_packages(id) ON DELETE SET NULL
);

CREATE INDEX idx_bookings_type ON bookings_inquiries(booking_type);
CREATE INDEX idx_bookings_status ON bookings_inquiries(status);
CREATE INDEX idx_bookings_created ON bookings_inquiries(created_at);
```

**Eloquent Model Casts:**

```php
protected $casts = [
    'booking_date' => 'date',
];
```

**Status Flow:**

```
new → contacted → confirmed → completed
                ↘ cancelled
```

| Status | Description |
|--------|-------------|
| `new` | Inquiry baru masuk, belum dihubungi |
| `contacted` | Admin sudah menghubungi pelanggan via WA |
| `confirmed` | Booking dikonfirmasi, jadwal dipastikan |
| `completed` | Layanan selesai |
| `cancelled` | Dibatalkan oleh pelanggan atau admin |

---

## 4. RBAC Matrix & Permission Table

### 4.1 Role Definitions

| Role | Code | Description |
|------|------|-------------|
| **Admin Web** | `admin_web` | Full system access — vehicles, testimonials, bookings, settings, user management |
| **Admin Tour** | `admin_tour` | Tour-focused — tour packages and tour bookings only |

### 4.2 Permission Matrix

| Resource / Endpoint | `admin_web` | `admin_tour` | Public |
|--------------------|:-----------:|:------------:|:------:|
| **Vehicles** | | | |
| GET /api/v1/vehicles (list) | ✅ | ❌ | ✅ |
| GET /api/v1/vehicles/{slug} (detail) | ✅ | ❌ | ✅ |
| POST /api/v1/admin/vehicles (create) | ✅ | ❌ | ❌ |
| PUT /api/v1/admin/vehicles/{id} (update) | ✅ | ❌ | ❌ |
| DELETE /api/v1/admin/vehicles/{id} (delete) | ✅ | ❌ | ❌ |
| **Tour Packages** | | | |
| GET /api/v1/tours (list) | ✅ | ✅ | ✅ |
| GET /api/v1/tours/{slug} (detail) | ✅ | ✅ | ✅ |
| POST /api/v1/admin/tours (create) | ✅ | ✅ | ❌ |
| PUT /api/v1/admin/tours/{id} (update) | ✅ | ✅ | ❌ |
| DELETE /api/v1/admin/tours/{id} (delete) | ✅ | ✅ | ❌ |
| **Testimonials** | | | |
| GET /api/v1/testimonials (list) | ✅ | ❌ | ✅ |
| POST /api/v1/admin/testimonials (create) | ✅ | ❌ | ❌ |
| PUT /api/v1/admin/testimonials/{id} (update) | ✅ | ❌ | ❌ |
| DELETE /api/v1/admin/testimonials/{id} (delete) | ✅ | ❌ | ❌ |
| PATCH /api/v1/admin/testimonials/{id}/approve | ✅ | ❌ | ❌ |
| **Bookings / Inquiries** | | | |
| POST /api/v1/inquiries (submit) | ✅ | ✅ | ✅ |
| GET /api/v1/admin/bookings (list all) | ✅ | ✅ (tour only) | ❌ |
| GET /api/v1/admin/bookings/{id} (detail) | ✅ | ✅ (tour only) | ❌ |
| PATCH /api/v1/admin/bookings/{id}/status | ✅ | ✅ (tour only) | ❌ |
| DELETE /api/v1/admin/bookings/{id} | ✅ | ❌ | ❌ |
| **Dashboard** | | | |
| GET /api/v1/admin/dashboard | ✅ | ✅ (limited) | ❌ |
| **Settings** | | | |
| GET /api/v1/admin/settings | ✅ | ❌ | ❌ |
| PUT /api/v1/admin/settings | ✅ | ❌ | ❌ |
| GET /api/v1/settings/public | ✅ | ✅ | ✅ |
| **User Management** | | | |
| GET /api/v1/admin/users | ✅ | ❌ | ❌ |
| POST /api/v1/admin/users | ✅ | ❌ | ❌ |
| PUT /api/v1/admin/users/{id} | ✅ | ❌ | ❌ |
| DELETE /api/v1/admin/users/{id} | ✅ | ❌ | ❌ |

### 4.3 `admin_tour` Booking Filter Rule

When `admin_tour` accesses `/api/v1/admin/bookings`, the query is automatically scoped:

```php
// In BookingController::index()
if ($request->user()->role === 'admin_tour') {
    $query->where('booking_type', 'tour');
}
```

---

## 5. RESTful API Endpoints Specification

### 5.1 Base URL & Versioning

```
Production:  https://api.fareltransport.com/api/v1
Development: http://localhost:8000/api/v1
```

### 5.2 Standard Response Structures

**Success (200):**

```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": { ... }
}
```

**Success with Pagination (200):**

```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 12,
    "total": 35,
    "from": 1,
    "to": 12
  }
}
```

**Created (201):**

```json
{
  "success": true,
  "message": "Resource created successfully",
  "data": { ... }
}
```

**Validation Error (422):**

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."],
    "price_half_day": ["The price half day must be a number."]
  }
}
```

**Unauthorized (401):**

```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

**Forbidden (403):**

```json
{
  "success": false,
  "message": "Unauthorized. Required role: admin_web"
}
```

**Server Error (500):**

```json
{
  "success": false,
  "message": "Internal server error"
}
```

### 5.3 Public Endpoints (No Auth)

#### 5.3.1 Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/auth/login` | Login for both admin roles |

**POST /auth/login — Request:**

```json
{
  "email": "admin@fareltransport.com",
  "password": "password"
}
```

**POST /auth/login — Response (200):**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin Web",
      "email": "admin@fareltransport.com",
      "role": "admin_web"
    },
    "token": "1|abc123def456ghi789...",
    "token_type": "Bearer"
  }
}
```

#### 5.3.2 Vehicles

| Method | Endpoint | Description | Query Params |
|--------|----------|-------------|--------------|
| `GET` | `/vehicles` | List available vehicles | `?category=MPV&search=avanza&per_page=12` |
| `GET` | `/vehicles/{slug}` | Vehicle detail | — |

#### 5.3.3 Tour Packages

| Method | Endpoint | Description | Query Params |
|--------|----------|-------------|--------------|
| `GET` | `/tours` | List active packages | `?duration_label=1 Hari&per_page=12` |
| `GET` | `/tours/{slug}` | Tour detail | — |

#### 5.3.4 Testimonials

| Method | Endpoint | Description | Query Params |
|--------|----------|-------------|--------------|
| `GET` | `/testimonials` | List approved testimonials | `?per_page=10` |

#### 5.3.5 Inquiries / Bookings

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/inquiries` | Submit booking inquiry |

**POST /inquiries — Request:**

```json
{
  "customer_name": "Budi Santoso",
  "phone_number": "08123456789",
  "package_or_vehicle_id": 1,
  "booking_type": "vehicle",
  "booking_date": "2026-08-01",
  "notes": "Mohon mobil dalam kondisi bersih."
}
```

**POST /inquiries — Response (201):**

```json
{
  "success": true,
  "message": "Inquiry submitted successfully. We will contact you via WhatsApp.",
  "data": {
    "id": 1,
    "customer_name": "Budi Santoso",
    "booking_type": "vehicle",
    "vehicle": {
      "id": 1,
      "name": "Toyota Avanza"
    },
    "booking_date": "2026-08-01",
    "status": "new",
    "wa_url": "https://wa.me/6281234567890?text=...",
    "created_at": "2026-07-24T15:30:00.000000Z"
  }
}
```

#### 5.3.6 Public Settings

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/settings/public` | Company info, contact, social links |

### 5.4 Admin Endpoints (Auth Required)

**All admin endpoints require:**

```
Authorization: Bearer {sanctum_token}
```

#### 5.4.1 Authenticated User Info

| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| `GET` | `/auth/me` | any | Get current user profile |
| `POST` | `/auth/logout` | any | Revoke current token |

**GET /auth/me — Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin Web",
    "email": "admin@fareltransport.com",
    "role": "admin_web"
  }
}
```

#### 5.4.2 Dashboard

| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/dashboard` | `admin_web` | Full statistics |
| `GET` | `/admin/dashboard` | `admin_tour` | Tour statistics only |

**GET /admin/dashboard — Response (admin_web):**

```json
{
  "success": true,
  "data": {
    "total_vehicles": 6,
    "total_tour_packages": 3,
    "total_inquiries": 45,
    "new_inquiries": 3,
    "today_inquiries": 2,
    "month_inquiries": 12,
    "recent_inquiries": [ ... ]
  }
}
```

#### 5.4.3 Vehicles CRUD (`admin_web` only)

| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/vehicles` | `admin_web` | List all (paginated) |
| `POST` | `/admin/vehicles` | `admin_web` | Create (multipart/form-data) |
| `GET` | `/admin/vehicles/{id}` | `admin_web` | Detail |
| `PUT` | `/admin/vehicles/{id}` | `admin_web` | Update |
| `DELETE` | `/admin/vehicles/{id}` | `admin_web` | Delete |

**POST /admin/vehicles — Request:**

```json
{
  "name": "Toyota Avanza",
  "category": "MPV",
  "passenger_capacity": 6,
  "transmission": "Manual",
  "price_half_day": 300000,
  "price_full_day": 500000,
  "description": "Kabin nyaman untuk keluarga",
  "image": "(file)",
  "badge": "Terpopuler",
  "inclusions": ["Driver", "AC", "BBM"],
  "is_available": true,
  "sort_order": 1
}
```

#### 5.4.4 Tour Packages CRUD (`admin_web` + `admin_tour`)

| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/tours` | `admin_web`, `admin_tour` | List all (paginated) |
| `POST` | `/admin/tours` | `admin_web`, `admin_tour` | Create |
| `GET` | `/admin/tours/{id}` | `admin_web`, `admin_tour` | Detail |
| `PUT` | `/admin/tours/{id}` | `admin_web`, `admin_tour` | Update |
| `DELETE` | `/admin/tours/{id}` | `admin_web`, `admin_tour` | Delete |

#### 5.4.5 Testimonials CRUD (`admin_web` only)

| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/testimonials` | `admin_web` | List all |
| `POST` | `/admin/testimonials` | `admin_web` | Create |
| `GET` | `/admin/testimonials/{id}` | `admin_web` | Detail |
| `PUT` | `/admin/testimonials/{id}` | `admin_web` | Update |
| `DELETE` | `/admin/testimonials/{id}` | `admin_web` | Delete |
| `PATCH` | `/admin/testimonials/{id}/approve` | `admin_web` | Toggle approval |

#### 5.4.6 Bookings/Inquiries Management

| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/bookings` | `admin_web` (all), `admin_tour` (tour only) | List inquiries |
| `GET` | `/admin/bookings/{id}` | `admin_web`, `admin_tour` | Detail |
| `PATCH` | `/admin/bookings/{id}/status` | `admin_web`, `admin_tour` | Update status |
| `DELETE` | `/admin/bookings/{id}` | `admin_web` | Delete |

**PATCH /admin/bookings/{id}/status — Request:**

```json
{
  "status": "confirmed",
  "notes": "Driver Budi akan jemput jam 07:00"
}
```

**Query Parameters:**

| Param | Type | Description |
|-------|------|-------------|
| `status` | string | Filter: new, contacted, confirmed, completed, cancelled |
| `booking_type` | string | Filter: vehicle, tour (admin_web only) |
| `date_from` | date | From date (YYYY-MM-DD) |
| `date_to` | date | To date (YYYY-MM-DD) |
| `search` | string | Search by customer name or phone |
| `per_page` | int | Items per page (default 15) |
| `sort` | string | Sort field (default: created_at) |
| `order` | string | asc / desc (default: desc) |

#### 5.4.7 Settings (`admin_web` only)

| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/settings` | `admin_web` | List all settings |
| `PUT` | `/admin/settings` | `admin_web` | Bulk update |

**PUT /admin/settings — Request:**

```json
{
  "settings": {
    "company_name": "Farel Transport",
    "whatsapp_number": "6281234567890",
    "company_address": "Jl. Malioboro No. 45, Yogyakarta"
  }
}
```

#### 5.4.8 User Management (`admin_web` only)

| Method | Endpoint | Role | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/users` | `admin_web` | List all admin users |
| `POST` | `/admin/users` | `admin_web` | Create new admin |
| `GET` | `/admin/users/{id}` | `admin_web` | User detail |
| `PUT` | `/admin/users/{id}` | `admin_web` | Update user |
| `DELETE` | `/admin/users/{id}` | `admin_web` | Delete user |

**POST /admin/users — Request:**

```json
{
  "name": "Admin Tour 1",
  "email": "tour@fareltransport.com",
  "password": "securepassword123",
  "password_confirmation": "securepassword123",
  "role": "admin_tour"
}
```

---

## 6. Business Logic & Middleware

### 6.1 Role Middleware — `EnsureUserRole`

```php
// app/Http/Middleware/EnsureUserRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!in_array($request->user()->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Required role: ' . implode(' or ', $roles),
            ], 403);
        }

        return $next($request);
    }
}
```

**Registration in `bootstrap/app.php`:**

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserRole::class,
    ]);
})
```

**Usage in Routes:**

```php
// admin_web only
Route::middleware(['auth:sanctum', 'role:admin_web'])->group(function () {
    Route::apiResource('vehicles', AdminVehicleController::class);
});

// admin_tour OR admin_web
Route::middleware(['auth:sanctum', 'role:admin_web,admin_tour'])->group(function () {
    Route::apiResource('tours', AdminTourController::class);
});
```

### 6.2 SQLite JSON Handling in Eloquent

SQLite stores JSON as TEXT. Laravel Eloquent `array` cast handles serialization/deserialization automatically:

```php
// Vehicle Model
protected $casts = [
    'inclusions' => 'array',    // TEXT '["Driver","AC"]' ↔ PHP array
];

// Querying JSON in SQLite (JSON1 extension)
$vehicles = Vehicle::whereJsonContains('inclusions', 'AC')->get();
```

**Important SQLite3 JSON Rules:**

| Operation | Eloquent | Raw SQL (JSON1) |
|-----------|----------|-----------------|
| Read | `$vehicle->inclusions` → `['Driver','AC']` | `json_extract(inclusions, '$')` |
| Contains | `whereJsonContains('inclusions', 'AC')` | `json_each(inclusions) WHERE value = 'AC'` |
| Write | `$vehicle->inclusions = ['Driver','AC','BBM']` | `json_set(inclusions, ...)` |

### 6.3 Dynamic WhatsApp URL Generator

```php
// app/Helpers/WhatsAppHelper.php

if (!function_exists('generateWaUrl')) {
    function generateWaUrl(string $number, string $message): string
    {
        $cleanNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $number));
        return sprintf('https://wa.me/%s?text=%s', $cleanNumber, urlencode($message));
    }
}
```

**Pre-filled Message Templates:**

| Context | Message |
|---------|---------|
| Vehicle inquiry | `"Halo, saya tertarik dengan {vehicle_name}. Mohon info ketersediaan."` |
| Tour inquiry | `"Halo, saya tertarik dengan paket {tour_name}. Mohon info lebih lanjut."` |
| General | `"Halo, saya ingin bertanya mengenai sewa mobil/paket tour."` |
| Booking confirmation | `"Halo, saya sudah mengirim inquiry. Nama: {name}. Mohon ditindaklanjuti."` |

### 6.4 Slug Generation

```php
// app/Models/Concerns/HasSlug.php

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($model->name);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . ++$counter;
                }

                $model->slug = $slug;
            }
        });
    }
}
```

---

## 7. Security & CORS

### 7.1 CORS Configuration

```php
// config/cors.php

'paths' => ['api/*'],
'allowed_origins' => [
    env('FRONTEND_URL', 'http://localhost:5173'),
],
'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
'allowed_headers' => ['Content-Type', 'Authorization', 'Accept'],
'supports_credentials' => true,
'max_age' => 86400,
```

### 7.2 Rate Limiting

| Endpoint | Limit | Window |
|----------|-------|--------|
| Public GET | 60 requests | per minute |
| Public POST (inquiry) | 10 requests | per minute |
| Admin endpoints | 120 requests | per minute |
| Login | 5 attempts | per minute |

### 7.3 Input Validation Rules

**StoreVehicleRequest:**

```php
public function rules(): array
{
    return [
        'name'               => 'required|string|max:255',
        'category'           => 'required|string|max:50',
        'passenger_capacity' => 'required|integer|min:1|max:60',
        'transmission'       => 'required|string|in:Manual,Matic',
        'price_half_day'     => 'required|numeric|min:0',
        'price_full_day'     => 'required|numeric|min:0|gte:price_half_day',
        'description'        => 'nullable|string|max:5000',
        'image'              => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        'badge'              => 'nullable|string|max:50',
        'inclusions'         => 'required|array|min:1',
        'inclusions.*'       => 'string|max:50',
        'is_available'       => 'boolean',
        'sort_order'         => 'integer|min:0',
    ];
}
```

**StoreInquiryRequest:**

```php
public function rules(): array
{
    return [
        'customer_name'       => 'required|string|max:255',
        'phone_number'        => 'required|string|max:20',
        'package_or_vehicle_id' => 'required|integer',
        'booking_type'        => 'required|in:vehicle,tour',
        'booking_date'        => 'required|date|after_or_equal:today',
        'notes'               => 'nullable|string|max:1000',
    ];
}
```

**LoginRequest:**

```php
public function rules(): array
{
    return [
        'email'    => 'required|email',
        'password' => 'required|string|min:6',
    ];
}
```

---

## 8. Environment Configuration

### 8.1 .env Variables

```env
APP_NAME="Farel Transport"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

FRONTEND_URL=http://localhost:5173

DB_CONNECTION=sqlite
# SQLite file at: database/database.sqlite

WA_NUMBER=6281234567890
WA_MESSAGE="Halo, saya ingin bertanya mengenai sewa mobil/paket tour."
COMPANY_ADDRESS="Jl. Malioboro No. 45, Yogyakarta"
OPERATIONAL_HOURS="08:00 - 20:00 WIB"
COMPANY_EMAIL=info@fareltransport.com

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173,127.0.0.1
```

### 8.2 Seeders

| Seeder | Description | Count |
|--------|-------------|-------|
| `DatabaseSeeder` | Master seeder | — |
| `UserSeeder` | 1 admin_web + 1 admin_tour | 2 |
| `VehicleSeeder` | 6 vehicles | 6 |
| `TourPackageSeeder` | 3 packages | 3 |
| `TestimonialSeeder` | 5 testimonials | 5 |
| `BookingInquirySeeder` | 10 sample inquiries | 10 |

### 8.3 Project Directory Structure

```
farelweb/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── Public/
│   │   │   │   │   ├── VehicleController.php
│   │   │   │   │   ├── TourController.php
│   │   │   │   │   ├── TestimonialController.php
│   │   │   │   │   ├── InquiryController.php
│   │   │   │   │   └── SettingController.php
│   │   │   │   ├── Admin/
│   │   │   │   │   ├── AuthController.php
│   │   │   │   │   ├── DashboardController.php
│   │   │   │   │   ├── VehicleController.php
│   │   │   │   │   ├── TourController.php
│   │   │   │   │   ├── TestimonialController.php
│   │   │   │   │   ├── BookingController.php
│   │   │   │   │   ├── SettingController.php
│   │   │   │   │   └── UserController.php
│   │   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   └── EnsureUserRole.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   └── LoginRequest.php
│   │   │   ├── Admin/
│   │   │   │   ├── StoreVehicleRequest.php
│   │   │   │   ├── StoreTourRequest.php
│   │   │   │   ├── StoreTestimonialRequest.php
│   │   │   │   ├── UpdateBookingStatusRequest.php
│   │   │   │   └── StoreUserRequest.php
│   │   │   └── Public/
│   │   │       └── StoreInquiryRequest.php
│   │   └── Resources/
│   │       ├── VehicleResource.php
│   │       ├── TourResource.php
│   │       ├── TestimonialResource.php
│   │       ├── BookingResource.php
│   │       ├── SettingResource.php
│   │       └── UserResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Vehicle.php
│   │   ├── TourPackage.php
│   │   ├── Testimonial.php
│   │   └── BookingInquiry.php
│   ├── Helpers/
│   │   └── WhatsAppHelper.php
│   └── Providers/
│       └── AppServiceProvider.php
├── config/
│   └── hiro.php
├── database/
│   ├── database.sqlite
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   └── api.php
└── storage/app/public/
    ├── vehicles/
    ├── packages/
    └── testimonials/
```

---

## Appendix A: API Route Registration

```php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Public\{VehicleController, TourController, TestimonialController, InquiryController, SettingController};
use App\Http\Controllers\Api\Admin\{AuthController, DashboardController, VehicleController as AdminVehicleController, TourController as AdminTourController, TestimonialController as AdminTestimonialController, BookingController, SettingController as AdminSettingController, UserController};

// ── Public Routes ─────────────────────────────────────
Route::prefix('v1')->group(function () {
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::get('/vehicles/{slug}', [VehicleController::class, 'show']);
    Route::get('/tours', [TourController::class, 'index']);
    Route::get('/tours/{slug}', [TourController::class, 'show']);
    Route::get('/testimonials', [TestimonialController::class, 'index']);
    Route::post('/inquiries', [InquiryController::class, 'store']);
    Route::get('/settings/public', [SettingController::class, 'public']);
});

// ── Auth Routes (no middleware) ────────────────────────
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
});

// ── Protected Routes ──────────────────────────────────
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Dashboard (both roles)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin_web,admin_tour');

    // Vehicles (admin_web only)
    Route::middleware('role:admin_web')->group(function () {
        Route::apiResource('admin/vehicles', AdminVehicleController::class);
    });

    // Tours (admin_web + admin_tour)
    Route::middleware('role:admin_web,admin_tour')->group(function () {
        Route::apiResource('admin/tours', AdminTourController::class);
    });

    // Testimonials (admin_web only)
    Route::middleware('role:admin_web')->group(function () {
        Route::apiResource('admin/testimonials', AdminTestimonialController::class);
        Route::patch('admin/testimonials/{testimonial}/approve', [AdminTestimonialController::class, 'approve']);
    });

    // Bookings (both roles, scoped in controller)
    Route::middleware('role:admin_web,admin_tour')->group(function () {
        Route::get('admin/bookings', [BookingController::class, 'index']);
        Route::get('admin/bookings/{booking}', [BookingController::class, 'show']);
        Route::patch('admin/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    });

    // Delete bookings (admin_web only)
    Route::middleware('role:admin_web')->group(function () {
        Route::delete('admin/bookings/{booking}', [BookingController::class, 'destroy']);
    });

    // Settings (admin_web only)
    Route::middleware('role:admin_web')->group(function () {
        Route::get('admin/settings', [AdminSettingController::class, 'index']);
        Route::put('admin/settings', [AdminSettingController::class, 'update']);
    });

    // Users (admin_web only)
    Route::middleware('role:admin_web')->group(function () {
        Route::apiResource('admin/users', UserController::class);
    });
});
```

---

## Appendix B: File Creation Checklist

| # | File | Description |
|---|------|-------------|
| 1 | `database/migrations/xxxx_create_vehicles_table.php` | Vehicles table |
| 2 | `database/migrations/xxxx_create_tour_packages_table.php` | Tour packages table |
| 3 | `database/migrations/xxxx_create_testimonials_table.php` | Testimonials table |
| 4 | `database/migrations/xxxx_create_bookings_inquiries_table.php` | Bookings/inquiries table |
| 5 | `database/migrations/xxxx_add_role_to_users_table.php` | User role field |
| 6 | `app/Models/Vehicle.php` | Vehicle model (with HasSlug) |
| 7 | `app/Models/TourPackage.php` | Tour package model (with HasSlug) |
| 8 | `app/Models/Testimonial.php` | Testimonial model |
| 9 | `app/Models/BookingInquiry.php` | Booking inquiry model |
| 10 | `app/Http/Middleware/EnsureUserRole.php` | Role middleware |
| 11 | `app/Http/Requests/Public/StoreInquiryRequest.php` | Inquiry validation |
| 12 | `app/Http/Requests/Admin/StoreVehicleRequest.php` | Vehicle validation |
| 13 | `app/Http/Requests/Admin/StoreTourRequest.php` | Tour validation |
| 14 | `app/Http/Requests/Auth/LoginRequest.php` | Login validation |
| 15 | `app/Http/Controllers/Api/Public/VehicleController.php` | Public vehicle API |
| 16 | `app/Http/Controllers/Api/Public/TourController.php` | Public tour API |
| 17 | `app/Http/Controllers/Api/Public/TestimonialController.php` | Public testimonial API |
| 18 | `app/Http/Controllers/Api/Public/InquiryController.php` | Public inquiry API |
| 19 | `app/Http/Controllers/Api/Public/SettingController.php` | Public settings API |
| 20 | `app/Http/Controllers/Api/Admin/AuthController.php` | Auth (login/logout/me) |
| 21 | `app/Http/Controllers/Api/Admin/DashboardController.php` | Dashboard stats |
| 22 | `app/Http/Controllers/Api/Admin/VehicleController.php` | Vehicle CRUD |
| 23 | `app/Http/Controllers/Api/Admin/TourController.php` | Tour CRUD |
| 24 | `app/Http/Controllers/Api/Admin/TestimonialController.php` | Testimonial CRUD |
| 25 | `app/Http/Controllers/Api/Admin/BookingController.php` | Booking management |
| 26 | `app/Http/Controllers/Api/Admin/SettingController.php` | Settings CRUD |
| 27 | `app/Http/Controllers/Api/Admin/UserController.php` | User management |
| 28 | `app/Http/Resources/VehicleResource.php` | Vehicle JSON resource |
| 29 | `app/Http/Resources/TourResource.php` | Tour JSON resource |
| 30 | `app/Http/Resources/BookingResource.php` | Booking JSON resource |
| 31 | `app/Helpers/WhatsAppHelper.php` | WA URL helper |
| 32 | `config/hiro.php` | App configuration |
| 33 | `routes/api.php` | All API routes |
| 34 | `database/seeders/UserSeeder.php` | Admin users |
| 35 | `database/seeders/VehicleSeeder.php` | Vehicle data |
| 36 | `database/seeders/TourPackageSeeder.php` | Tour data |
| 37 | `database/seeders/TestimonialSeeder.php` | Testimonial data |
| 38 | `database/seeders/BookingInquirySeeder.php` | Inquiry data |
