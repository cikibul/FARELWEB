# Technical Design Document — Hiro Transport Backend API

## 1. Arsitektur Aplikasi

```
Laravel 11 (Laravel 13.x) + Sanctum + SQLite / MySQL

app/
  Http/
    Controllers/Api/
      Public/     -> CarController, ServiceController, BookingController, TestimonialController, FaqController
      Admin/      -> AuthController, CarController, ServiceController, BookingController, TestimonialController, FaqController, DashboardController
    Requests/     -> StoreBookingRequest, StoreCarRequest, UpdateCarRequest, StoreTestimonialRequest, StoreFaqRequest, StoreServiceRequest
    Resources/    -> CarResource, ServiceResource, BookingResource, TestimonialResource, FaqResource
  Models/         -> User, Car, Service, Booking, Testimonial, Faq
  Notifications/  -> BookingConfirmation (to customer), NewBookingNotification (to admin)
  Services/       -> BookingPriceCalculator

database/
  migrations/     -> create_cars_table, create_services_table, create_bookings_table, create_testimonials_table, create_faqs_table
  seeders/        -> DatabaseSeeder, CarSeeder, ServiceSeeder, TestimonialSeeder, FaqSeeder, AdminUserSeeder

routes/
  api.php         -> Semua endpoint API v1

config/
  sanctum.php     -> Konfigurasi Sanctum
  hiro.php        -> Konfigurasi custom (nomor WA admin, dll)
```

## 2. Database Schema

### 2.1 Tabel: `users` (default Laravel + tambahan role)
| Column | Type | Keterangan |
|--------|------|------------|
| id | bigIncrements | PK |
| name | string | |
| email | string(191) | unique |
| password | string | hashed |
| role | enum('admin','superadmin') | default 'admin' |
| phone | string(20) | nullable, nomor WA admin |
| timestamps | | |

### 2.2 Tabel: `cars`
| Column | Type | Keterangan |
|--------|------|------------|
| id | bigIncrements | PK |
| name | string | Nama mobil (e.g. Toyota Avanza) |
| slug | string(191) | unique, auto-generated |
| brand | string | Merk (Toyota, Daihatsu, dll) |
| category | string | SUV, MPV, City Car, Micro Bus, Bus, Wedding Car |
| passenger_capacity | integer | Kapasitas penumpang |
| transmission | string | Manual / Matic |
| price_per_day | decimal(12,2) | Harga sewa per hari |
| price_per_package | decimal(12,2) | nullable, harga paket |
| description | text | nullable |
| photos | json | nullable, array of foto paths |
| is_available | boolean | default true |
| sort_order | integer | default 0 |
| timestamps | | |

### 2.3 Tabel: `services`
| Column | Type | Keterangan |
|--------|------|------------|
| id | bigIncrements | PK |
| name | string | Nama layanan |
| slug | string(191) | unique |
| type | string | driver, lepas_kunci, drop_off, paket_wisata, wedding |
| description | text | nullable |
| icon | string | nullable, icon class/path |
| base_price | decimal(12,2) | nullable |
| is_active | boolean | default true |
| sort_order | integer | default 0 |
| timestamps | | |

### 2.4 Tabel: `bookings`
| Column | Type | Keterangan |
|--------|------|------------|
| id | bigIncrements | PK |
| booking_code | string(20) | unique, auto-generated (e.g. HT-20260723-001) |
| customer_name | string | |
| customer_phone | string(20) | |
| customer_email | string | nullable |
| pickup_location | string | Lokasi jemput |
| service_type | string | driver, lepas_kunci, drop_off, paket_wisata, wedding |
| car_id | foreignId | nullable, relasi ke cars |
| service_id | foreignId | nullable, relasi ke services |
| start_date | date | Tanggal mulai sewa |
| end_date | date | Tanggal selesai sewa |
| total_days | integer | Jumlah hari (otomatis) |
| price_per_day | decimal(12,2) | nullable |
| service_fee | decimal(12,2) | default 0 |
| total_price | decimal(12,2) | Total harga |
| status | string | pending, confirmed, completed, cancelled |
| notes | text | nullable, catatan pelanggan |
| admin_notes | text | nullable, catatan admin |
| timestamps | | |

### 2.5 Tabel: `testimonials`
| Column | Type | Keterangan |
|--------|------|------------|
| id | bigIncrements | PK |
| customer_name | string | |
| rating | integer | 1-5 |
| content | text | Isi testimoni |
| photo | string | nullable |
| is_approved | boolean | default false |
| sort_order | integer | default 0 |
| timestamps | | |

### 2.6 Tabel: `faqs`
| Column | Type | Keterangan |
|--------|------|------------|
| id | bigIncrements | PK |
| question | string | |
| answer | text | |
| is_active | boolean | default true |
| sort_order | integer | default 0 |
| timestamps | | |

## 3. Relasi

```
Booking -> belongsTo Car
Booking -> belongsTo Service
Car -> hasMany Booking
Service -> hasMany Booking
```

## 4. Struktur Routes (`routes/api.php`)

```
Prefix: /api/v1

PUBLIC (no auth):
  GET    /public/cars                       -> Public\CarController@index
  GET    /public/cars/{slug}                -> Public\CarController@show
  GET    /public/services                   -> Public\ServiceController@index
  GET    /public/testimonials               -> Public\TestimonialController@index
  GET    /public/faqs                       -> Public\FaqController@index
  POST   /public/bookings                   -> Public\BookingController@store

ADMIN (sanctum auth):
  POST   /admin/login                       -> Admin\AuthController@login
  POST   /admin/logout                      -> Admin\AuthController@logout
  GET    /admin/dashboard                   -> Admin\DashboardController@index

  GET    /admin/cars                        -> Admin\CarController@index
  POST   /admin/cars                        -> Admin\CarController@store
  GET    /admin/cars/{id}                   -> Admin\CarController@show
  PUT    /admin/cars/{id}                   -> Admin\CarController@update
  DELETE /admin/cars/{id}                   -> Admin\CarController@destroy

  GET    /admin/services                    -> Admin\ServiceController@index
  POST   /admin/services                    -> Admin\ServiceController@store
  GET    /admin/services/{id}               -> Admin\ServiceController@show
  PUT    /admin/services/{id}               -> Admin\ServiceController@update
  DELETE /admin/services/{id}               -> Admin\ServiceController@destroy

  GET    /admin/bookings                    -> Admin\BookingController@index
  GET    /admin/bookings/{id}               -> Admin\BookingController@show
  PUT    /admin/bookings/{id}/status        -> Admin\BookingController@updateStatus
  DELETE /admin/bookings/{id}               -> Admin\BookingController@destroy

  GET    /admin/testimonials                -> Admin\TestimonialController@index
  POST   /admin/testimonials                -> Admin\TestimonialController@store
  GET    /admin/testimonials/{id}           -> Admin\TestimonialController@show
  PUT    /admin/testimonials/{id}           -> Admin\TestimonialController@update
  DELETE /admin/testimonials/{id}           -> Admin\TestimonialController@destroy
  PUT    /admin/testimonials/{id}/approve   -> Admin\TestimonialController@approve

  GET    /admin/faqs                        -> Admin\FaqController@index
  POST   /admin/faqs                        -> Admin\FaqController@store
  GET    /admin/faqs/{id}                   -> Admin\FaqController@show
  PUT    /admin/faqs/{id}                   -> Admin\FaqController@update
  DELETE /admin/faqs/{id}                   -> Admin\FaqController@destroy
```

## 5. Alur Perhitungan Harga Booking

```
1. Hitung jumlah hari: end_date - start_date + 1
2. Ambil price_per_day dari car yang dipilih (jika ada)
3. Hitung total = total_days * price_per_day
4. Jika ada service_fee (dari services), tambahkan
5. Simpan total_price ke booking
```

## 6. Format Respons JSON (API Resource)

### Sukses
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": { ... }
}
```

### Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": { ... }
}
```

### Pagination
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 48
  }
}
```

## 7. Notifikasi

### Booking Confirmation (ke Pelanggan)
- Channel: Email (Mail) + WhatsApp (HTTP ke Fonnte/Wablas)
- Trigger: setelah booking dibuat (status pending)
- Konten: Booking code, detail mobil, tanggal, total harga, status

### New Booking Notification (ke Admin)
- Channel: Email + WhatsApp
- Trigger: setelah booking dibuat
- Konten: Data pelanggan, mobil dipilih, tanggal, link admin

### Status Update Notification (ke Pelanggan)
- Trigger: saat admin mengubah status booking
- Konten: Status baru, pesan konfirmasi

## 8. Keamanan

- **Sanctum Token**: Admin login mendapatkan token, dikirim via Bearer header
- **Rate Limiting**: Public endpoints: 30 req/min; Admin endpoints: 60 req/min
- **CORS**: Konfigurasi di config/cors.php untuk allow frontend domain
- **Validasi**: Form Request untuk semua input data
- **Mass Assignment**: Guarded/larit fillable di setiap model

## 9. File yang Akan Dibuat

| No | File | Lokasi |
|----|------|--------|
| 1 | Migration cars | database/migrations/xxxx_create_cars_table.php |
| 2 | Migration services | database/migrations/xxxx_create_services_table.php |
| 3 | Migration bookings | database/migrations/xxxx_create_bookings_table.php |
| 4 | Migration testimonials | database/migrations/xxxx_create_testimonials_table.php |
| 5 | Migration faqs | database/migrations/xxxx_create_faqs_table.php |
| 6 | Migration add_role_to_users | database/migrations/xxxx_add_role_to_users_table.php |
| 7 | Model Car | app/Models/Car.php |
| 8 | Model Service | app/Models/Service.php |
| 9 | Model Booking | app/Models/Booking.php |
| 10 | Model Testimonial | app/Models/Testimonial.php |
| 11 | Model Faq | app/Models/Faq.php |
| 12 | Request StoreBookingRequest | app/Http/Requests/StoreBookingRequest.php |
| 13 | Request StoreCarRequest | app/Http/Requests/StoreCarRequest.php |
| 14 | Request UpdateCarRequest | app/Http/Requests/UpdateCarRequest.php |
| 15 | Request StoreServiceRequest | app/Http/Requests/StoreServiceRequest.php |
| 16 | Request StoreTestimonialRequest | app/Http/Requests/StoreTestimonialRequest.php |
| 17 | Request StoreFaqRequest | app/Http/Requests/StoreFaqRequest.php |
| 18 | Resource CarResource | app/Http/Resources/CarResource.php |
| 19 | Resource ServiceResource | app/Http/Resources/ServiceResource.php |
| 20 | Resource BookingResource | app/Http/Resources/BookingResource.php |
| 21 | Resource TestimonialResource | app/Http/Resources/TestimonialResource.php |
| 22 | Resource FaqResource | app/Http/Resources/FaqResource.php |
| 23 | Controller Public\CarController | app/Http/Controllers/Api/Public/CarController.php |
| 24 | Controller Public\ServiceController | app/Http/Controllers/Api/Public/ServiceController.php |
| 25 | Controller Public\BookingController | app/Http/Controllers/Api/Public/BookingController.php |
| 26 | Controller Public\TestimonialController | app/Http/Controllers/Api/Public/TestimonialController.php |
| 27 | Controller Public\FaqController | app/Http/Controllers/Api/Public/FaqController.php |
| 28 | Controller Admin\AuthController | app/Http/Controllers/Api/Admin/AuthController.php |
| 29 | Controller Admin\DashboardController | app/Http/Controllers/Api/Admin/DashboardController.php |
| 30 | Controller Admin\CarController | app/Http/Controllers/Api/Admin/CarController.php |
| 31 | Controller Admin\ServiceController | app/Http/Controllers/Api/Admin/ServiceController.php |
| 32 | Controller Admin\BookingController | app/Http/Controllers/Api/Admin/BookingController.php |
| 33 | Controller Admin\TestimonialController | app/Http/Controllers/Api/Admin/TestimonialController.php |
| 34 | Controller Admin\FaqController | app/Http/Controllers/Api/Admin/FaqController.php |
| 35 | Service BookingPriceCalculator | app/Services/BookingPriceCalculator.php |
| 36 | Notification BookingConfirmation | app/Notifications/BookingConfirmation.php |
| 37 | Notification NewBookingNotification | app/Notifications/NewBookingNotification.php |
| 38 | Routes API | routes/api.php |
| 39 | Config hiro | config/hiro.php |
| 40 | Seeder AdminUserSeeder | database/seeders/AdminUserSeeder.php |
| 41 | Seeder CarSeeder | database/seeders/CarSeeder.php |
| 42 | Seeder ServiceSeeder | database/seeders/ServiceSeeder.php |
| 43 | Seeder TestimonialSeeder | database/seeders/TestimonialSeeder.php |
| 44 | Seeder FaqSeeder | database/seeders/FaqSeeder.php |
| 45 | Update DatabaseSeeder | database/seeders/DatabaseSeeder.php |
| 46 | Update User model | app/Models/User.php |
| 47 | .env update | .env |

## 10. Petunjuk Instalasi & Setup

```bash
# 1. Clone / masuk ke project
cd farelweb

# 2. Install dependensi
composer install

# 3. Install Sanctum (jika belum)
composer require laravel/sanctum
php artisan install:api

# 4. Copy environment dan generate key
copy .env.example .env
php artisan key:generate

# 5. Setup database di .env (SQLITE default, bisa ganti ke MySQL)
# DB_CONNECTION=sqlite (default, sudah ada database.sqlite)

# 6. Jalankan migration & seeder
php artisan migrate:fresh --seed

# 7. Jalankan server
php artisan serve

# 8. Testing endpoint dengan curl / Postman
```

## 11. Testing Endpoint (Contoh)

### Public
```bash
# Daftar mobil
curl http://localhost:8000/api/v1/public/cars

# Detail mobil
curl http://localhost:8000/api/v1/public/cars/toyota-avanza

# Kirim booking
curl -X POST http://localhost:8000/api/v1/public/bookings \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Budi",
    "customer_phone": "08123456789",
    "pickup_location": "Solo",
    "service_type": "driver",
    "car_id": 1,
    "start_date": "2026-07-24",
    "end_date": "2026-07-26"
  }'
```

### Admin
```bash
# Login
curl -X POST http://localhost:8000/api/v1/admin/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@hirotransport.com", "password": "password"}'

# Response: {"token": "1|abc123..."}

# Gunakan token untuk akses admin endpoints
curl http://localhost:8000/api/v1/admin/dashboard \
  -H "Authorization: Bearer 1|abc123..."

# Update status booking
curl -X PUT http://localhost:8000/api/v1/admin/bookings/1/status \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{"status": "confirmed"}'
```