# Laravel + MySQL Migration Blueprint

## Goal

Replace Firebase Auth, Firestore, and the old Next server actions with a Laravel 12 API backed by MySQL/MariaDB, while keeping the existing Next.js frontend as the UI.

Laravel 12 is the right local target because this XAMPP machine has PHP 8.2.12. Laravel 13 requires PHP 8.3+, while Laravel 12 supports PHP 8.2.

## Migrated Backend Surface

The application now stores and reads data through Laravel API routes instead of Firebase client SDKs.

- Auth: Laravel Sanctum token authentication with role redirects in the Next app.
- Data: MySQL tables for users, services, vendor services, settings, service requests, KYC profiles, storefront settings, and archived clients.
- Uploads: `POST /api/uploads` stores files through Laravel's public storage disk.
- Email: SMTP settings are stored in MySQL and ready to be wired into Laravel mail services.
- Payments: Paystack/Credo inline scripts call Laravel request payment endpoints after callback.
- Authorization: Laravel middleware and controller policies restrict admin, vendor, and client API surfaces.

## Laravel App Shape

The separate Laravel backend lives in `backend/` and exposes JSON API routes under `/api`.

The Laravel app should expose a JSON API under `/api` and store uploaded files through Laravel's storage disk.

Authentication should use Laravel Sanctum tokens for the Next frontend:

- `POST /api/auth/register`
- `POST /api/auth/register-client`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`
- `POST /api/auth/forgot-password`

Google sign-in can be added later through Socialite or replaced with standard email/password first.

## MySQL Schema

### users

Maps Firebase `users/{uid}`.

- `id` bigint primary key
- `first_name` varchar
- `last_name` varchar
- `email` varchar unique
- `password` varchar nullable for OAuth-only accounts later
- `phone` varchar nullable
- `country` varchar nullable
- `state` varchar nullable
- `role` enum: `admin`, `vendor`, `client`
- `status` enum: `Approved`, `Pending`, `Rejected`, `Disabled`
- `avatar_url` varchar nullable
- `registered_by_vendor_id` foreign key nullable to `users.id`
- `email_verified_at` timestamp nullable
- timestamps

### kyc_profiles

One-to-one with vendor users.

- `id` bigint primary key
- `user_id` foreign key unique
- `nin` varchar nullable
- `id_card_url` varchar nullable
- `proof_of_address_url` varchar nullable
- `personal_image_url` varchar nullable
- `bank_name` varchar nullable
- `account_name` varchar nullable
- `account_number` varchar nullable
- `terms_agreed` boolean default false
- `privacy_agreed` boolean default false
- `data_consent_agreed` boolean default false
- `digital_signature` varchar nullable
- `status` enum: `NotStarted`, `Submitted`, `Rejected`, `Approved`
- `rejection_reason` text nullable
- timestamps

### storefront_settings

One-to-one with vendor users.

- `id` bigint primary key
- `user_id` foreign key unique
- `storefront_name` varchar nullable
- `storefront_about` text nullable
- `theme` enum: `light`, `dark`
- `color_palette` enum: `default`, `blue`, `green`, `purple`, `red`, `orange`, `yellow`, `teal`
- `background_image_url` varchar nullable
- `gradient` varchar nullable
- timestamps

### services

Maps Firestore `services`.

- `id` bigint primary key
- `name` varchar
- `price` decimal(12,2)
- `description` text
- `image_url` varchar nullable
- `status` enum: `Active`, `Inactive`
- timestamps

### vendor_services

Maps Firestore `users/{vendorId}/myServices`.

- `id` bigint primary key
- `vendor_id` foreign key to `users.id`
- `service_id` foreign key to `services.id`
- `name` varchar
- `price` decimal(12,2)
- `description` text
- `image_url` varchar nullable
- `status` enum: `Active`, `Inactive`
- unique index: `vendor_id`, `service_id`
- timestamps

### service_requests

Maps Firestore `serviceRequests`.

- `id` bigint primary key
- `service_id` foreign key nullable to `services.id`
- `vendor_service_id` foreign key nullable to `vendor_services.id`
- `service_name` varchar
- `price` decimal(12,2)
- `vendor_id` foreign key to `users.id`
- `vendor_name` varchar nullable snapshot
- `client_id` foreign key to `users.id`
- `client_name` varchar snapshot
- `client_email` varchar snapshot
- `status` enum: `Awaiting Payment`, `Pending`, `Processing`, `Completed`, `Cancelled`
- `payment_reference` varchar nullable
- `payment_gateway` enum nullable: `paystack`, `credo`
- `payment_status` enum: `Paid`, `Unpaid`
- `documents` json nullable
- timestamps

### system_settings

Single-row platform settings, replacing `settings/global`.

- `id` bigint primary key
- `platform_name` varchar nullable
- `logo_url` varchar nullable
- `default_currency` varchar default `USD`
- `maintenance_mode` boolean default false
- `payment_gateway` enum nullable: `paystack`, `credo`
- `payments_enabled` boolean default true
- `payment_mode` enum: `test`, `live`
- Paystack public/secret/base URL fields
- Credo public/secret/base URL fields
- SMTP host/port/user/pass/encryption/sender fields
- Template text fields for vendor approval, client registration, payment confirmation, service update
- timestamps

### archived_clients

Maps `users/{vendorId}/archivedClients`.

- `id` bigint primary key
- `vendor_id` foreign key to `users.id`
- `client_id` foreign key nullable to `users.id`
- `client_name` varchar
- `client_email` varchar
- `metadata` json nullable
- timestamps

## API Route Map

### Public

- `GET /api/public/vendors/{vendor}/storefront`
- `GET /api/public/settings`

### Authenticated User

- `GET /api/auth/me`
- `PATCH /api/auth/me`
- `POST /api/uploads`

### Admin

- `GET /api/admin/dashboard`
- `GET /api/admin/users`
- `PATCH /api/admin/users/{user}`
- `DELETE /api/admin/users/{user}`
- `GET /api/admin/vendors`
- `GET /api/admin/approvals`
- `GET /api/admin/approvals/{vendor}`
- `POST /api/admin/approvals/{vendor}/approve`
- `POST /api/admin/approvals/{vendor}/reject`
- `GET /api/admin/services`
- `POST /api/admin/services`
- `PATCH /api/admin/services/{service}`
- `DELETE /api/admin/services/{service}`
- `GET /api/admin/settings`
- `PATCH /api/admin/settings`

### Vendor

- `GET /api/vendor/dashboard`
- `GET /api/vendor/kyc`
- `POST /api/vendor/kyc`
- `GET /api/vendor/services`
- `POST /api/vendor/services`
- `PATCH /api/vendor/services/{vendorService}`
- `DELETE /api/vendor/services/{vendorService}`
- `GET /api/vendor/requests`
- `PATCH /api/vendor/requests/{serviceRequest}`
- `GET /api/vendor/clients`
- `PATCH /api/vendor/settings/profile`
- `PATCH /api/vendor/settings/storefront`

### Client

- `GET /api/client/dashboard`
- `GET /api/client/services`
- `GET /api/client/requests`
- `POST /api/client/requests`
- `DELETE /api/client/requests/{serviceRequest}`
- `GET /api/client/book/{vendor}/{vendorService}`
- `POST /api/client/requests/{serviceRequest}/payment`
- `PATCH /api/client/settings`

## Authorization Rules

- Admin can manage users, vendors, services, approvals, settings, and all service requests.
- Vendor can read/update their own vendor profile, KYC, storefront, vendor services, clients, and requests assigned to them.
- Client can read/update their own profile and requests, and can create requests for active vendor services.
- Public users can view approved vendor storefronts and active vendor services.
- Payment confirmation should be verified server-side before marking requests as paid. The current client-only callback update should be treated as temporary behavior.

## Completed Migration Work

1. Scaffolded Laravel 12 in `backend/` with Sanctum and MySQL configuration.
2. Added migrations/models/relationships for the schema above.
3. Added seeders for an admin user, system settings, and starter services.
4. Implemented auth, public, admin, vendor, client, upload, settings, KYC, service, request, and booking endpoints.
5. Replaced Firebase usage in the Next frontend with `src/lib/api.ts`.
6. Removed Firebase packages, config files, rules files, Nodemailer frontend helper, and the old upload server action.

## Remaining Hardening

Payment callback marking currently trusts the browser callback and should be replaced with server-side gateway verification or webhooks before production payment use.
