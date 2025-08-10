# Project Structure

This project is a Laravel application that integrates the Eventmie Pro package (with Voyager and Vue). Below is a quick map of where things live and how they connect.

## Top-level

- `app/` `routes/` `config/` `resources/` `database/` `public/`: Standard Laravel app shell
- `routes/web.php`: Initializes Eventmie routes via `Eventmie::routes()` and can host app-specific routes
- `eventmie-pro/`: Local package (editable) providing most functionality

## Eventmie Pro package

- `eventmie-pro/src/EventmieServiceProvider.php`
  - Registers third-party providers (Voyager, Socialite, Ziggy, DomPDF, Charts, DataTables, Honeypot)
  - Aliases middlewares (auth/admin/organiser/customer/common/everified)
  - Loads views/translations/migrations and merges config

- `eventmie-pro/routes/eventmie.php`
  - Defines all site and admin routes (welcome, events, bookings, venues, dashboard, voyager overrides)

- `eventmie-pro/src/Http/Controllers/`
  - All controllers (frontend and `Voyager/*` admin overrides)

- `eventmie-pro/src/Models/`
  - Core models: `Event`, `Ticket`, `Booking`, `Venue`, `User`, etc.

- `eventmie-pro/resources/views/`
  - Blade templates (namespaced as `eventmie::...`)
  - Override by placing files under `resources/views/vendor/eventmie/...`

- `eventmie-pro/resources/js/`
  - Vue code organized per feature/page (e.g. `welcome/`, `events_manage/`)
  - Built via Laravel Mix into `eventmie-pro/publishable/assets/js/*.js`

- `eventmie-pro/publishable/assets/`
  - Compiled CSS/JS/webfonts served via `eventmie_asset()` helper and `frontend-assets` route

- `eventmie-pro/publishable/config/`
  - Package configs (`eventmie.php`, `voyager.php`, `charts.php`, `datatables.php`)

- `eventmie-pro/publishable/lang/`
  - Translations loaded as `eventmie-pro::`

- `eventmie-pro/publishable/database/migrations`
  - Package migrations (autoloaded unless disabled by config)

## How a request flows

1. Request → `routes/web.php` → `Eventmie::routes()` → `eventmie-pro/routes/eventmie.php`
2. Controller returns a view like `eventmie::welcome`
3. Layout `eventmie::layouts.app` mounts `#eventmie_app`
4. Page-specific JS (from `eventmie-pro/resources/js/...`) compiled to `publishable/assets` is injected via `eventmie_asset('js/xyz.js')` and initializes Vue

## Admin (Voyager)

- Admin base path: `/admin` (configurable)
- Routes extended/overridden by controllers in `eventmie-pro/src/Http/Controllers/Voyager`
- Views in `eventmie-pro/resources/views/vendor/voyager`

## Customization guidelines

- Controllers: set `config('eventmie.controllers.namespace')` to point routes to your app controllers, or edit package controllers directly (local path repo)
- Views: override with `resources/views/vendor/eventmie/...`
- Assets: edit `eventmie-pro/resources/js|sass`, build in `eventmie-pro` with Mix (`npm run watch` or `npm run production`)
- Translations: add under `resources/lang/vendor/eventmie-pro/...`

