# QA_STATUS

**Honest summary:** this project was written in a sandbox that has **no PHP, no Composer, no MySQL and no internet**.
Because of that, nothing could be *executed*. Only static checks were run. The project has **not** been run end-to-end, so do not assume it is bug-free. Please run the checklist at the bottom on your PC and report any failure.

## Tests performed (in the sandbox)
| Check | Result |
|---|---|
| All 67 plain PHP files: balanced `{}` `()` `[]` after stripping strings/comments, `<?php` header present (heuristic script) | PASS |
| All 25 Blade views: balanced `@if/@foreach/@forelse/@unless/@error/@push/@php/@section` and `{{ }}` | PASS |
| Every `view()`, `@extends`, `@include` target file exists | PASS |
| Every `route('name')` used in PHP/Blade is defined in `routes/web.php` | PASS |
| Every `use App\...` / `Database\Seeders\...` import points to an existing file | PASS |
| Every translation key used in views/code exists in both `lang/en/ui.php` and `lang/bn/ui.php`; both files have identical key sets (180 keys) | PASS |
| ZIP integrity (`zip -T`), non-empty, key files present | see final delivery message |

## Tests written but NOT run (need PHP)
`tests/Feature` contains 34 automated tests covering: public pages, seeded content, Bangla switch, 404 page, sitemap/robots, CV download, no public registration, contact form (valid / invalid / honeypot / rate limit), admin auth (guest redirect, login, wrong password, non-admin 403, rate limit, logout, `portfolio:create-admin` command), CRUD for projects/skills/certificates/profile/about/CV/social links, uploads (replace, delete old file, reject .php/.exe, size limit), reordering, messages, settings, language overrides, password change.
**Status: NOT PERFORMED** - run `php artisan test`.

## Tests not performed at all
- `composer install` / `composer validate` (no Composer, no network). Note: there is no `composer.lock`; Composer will create it.
- `php -l` syntax lint of every file, `php artisan route:list`, `php artisan migrate` on MySQL, `php artisan test`.
- Manual browser checks: responsive layouts (the CSS uses Bootstrap grid/breakpoints, but was not viewed), dark/light switch, language switch UI, smooth scrolling, animations, file-preview JS, admin navigation click-through.
- `setup_windows.ps1` was not executed (no PowerShell available); it was written carefully but may contain mistakes.
- Accessibility audit, performance measurement, security scan.

## Tests failed
None observed - but only because nothing beyond the static checks could be executed.

## Known limitations / things to review
- Uses CDNs (Bootstrap, Font Awesome, Google Fonts, Chart.js on the dashboard): needs internet in the browser.
- Seeded text is minimal and generic on purpose; **skill levels are placeholders**. No dates/roles/awards were invented. Please edit in the dashboard.
- Bangla translations were written by the assistant; have a native reader review wording (editable under *Language Content*).
- Gallery has no public section unless at least one item exists. Project "details" use a dedicated page (`/projects/{slug}`), not a modal.
- "Language Content" overrides UI strings (labels/buttons/nav). Content records (projects, etc.) have separate EN/BN fields.
- Validation messages are translated for the rules used by this project only.
- Contact messages are stored and shown in the dashboard; no email notification is sent.
- Targets `laravel/framework ^11.0` (latest 11.x). Not Laravel 12.

## Checklist to verify on your PC (about 10 minutes)
1. `composer install` finishes without errors.
2. `php artisan route:list` shows `admin.*` routes.
3. `php artisan migrate --seed` succeeds on MySQL.
4. `php artisan test` - all green (needs `pdo_sqlite`, and `gd` for upload tests).
5. Log in at `/admin/login`, add a project with an image, see it on the home page.
6. Switch language and theme; resize the browser to phone width.
7. Send a contact message, read it in the dashboard.
