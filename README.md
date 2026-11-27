# MD. HABIB KHAN - Personal Portfolio (Laravel 12)

A bilingual (English / বাংলা) portfolio website with a private admin dashboard.
You can add and edit **all** content (projects, skills, certificates, volunteering, CV, social links...) from the dashboard. You never need to edit code for normal content.

> **Please read `QA_STATUS.md`.** This project was written in an environment without PHP, so the automated tests were written but could **not** be run before delivery. Running them on your PC is step 14 below.

---

## 1. Software you need
- **PHP 8.2 or newer** (WAMP Server includes it). Required extensions: mbstring, openssl, pdo_mysql, fileinfo, ctype, tokenizer, xml (and `gd` for the image tests).
- **MySQL** (WAMP includes it) and phpMyAdmin.
- **Composer**: https://getcomposer.org/download/
- **VS Code**: https://code.visualstudio.com/
- Internet connection (first install, plus Bootstrap/Font Awesome/Google Fonts are loaded from CDNs).

Tip for WAMP: PHP must be reachable from PowerShell. Add the WAMP PHP folder (e.g. `C:\wamp64\bin\php\php8.3.x`) to the Windows PATH and open a **new** terminal. Check with `php -v` and `composer -V`.

## 2. Extract the ZIP
Right-click `MD_Habib_Khan_Professional_Portfolio.zip` > Extract All. A good place is `C:\wamp64\www\` (or any folder, e.g. `C:\Projects\`).

## 3. Open in VS Code
VS Code > File > Open Folder > choose the extracted `MD_Habib_Khan_Professional_Portfolio` folder. Open the terminal with **Terminal > New Terminal** (PowerShell).

## 4. Create the MySQL database
Start WAMP (green icon) > open phpMyAdmin > **Databases** > name `portfolio_db`, collation `utf8mb4_unicode_ci` > **Create**.

## Fast way (recommended): run the setup script
```powershell
powershell -ExecutionPolicy Bypass -File .\setup_windows.ps1
```
It does steps 5-11 for you and asks for your database details and admin email/password. If you use it, jump to step 12. Otherwise follow the manual steps.

## 5. Configure `.env`
```powershell
copy .env.example .env
```
Open `.env` and set (WAMP default has user `root` and an empty password):
```
DB_DATABASE=portfolio_db
DB_USERNAME=root
DB_PASSWORD=
```
## 6. Install Composer packages
```powershell
composer install
```
## 7. Generate the application key
```powershell
php artisan key:generate
```
## 8. Create tables and initial content
```powershell
php artisan migrate --seed
```
## 9. Create the storage link (needed to show uploaded images)
```powershell
php artisan storage:link
```
If this fails with a permission error, open PowerShell **as Administrator** (or enable Windows Developer Mode) and run it again.
## 10. Create your admin account
```powershell
php artisan portfolio:create-admin
```
Enter your email and a strong password (min 10 characters, letters and numbers). There is **no default password** and no public registration page. Running the command again with the same email resets that account's password.

## 11. Run the website
```powershell
php artisan serve
```
Open http://localhost:8000

## 12. Log in to the admin dashboard
http://localhost:8000/admin/login - use the email and password from step 10. Login is rate-limited (5 tries per minute).

## 13. How to manage content
| I want to... | Go to (sidebar) |
|---|---|
| Change name, headline, photo, email | Profile |
| Edit the About text | About Me |
| Add a project (image, links, technologies) | Projects > Add new |
| Add or edit skills / categories | Skills, Skill Categories (set level 0-100 or leave empty) |
| Add education / experience / achievements | Education, Experience, Achievements |
| Add volunteer details (role, date, description, image) | Volunteer Experience |
| Upload a certificate (image or PDF) | Certificates |
| Upload your CV (PDF/DOC/DOCX) | CV / Resume |
| Change social links | Social Links |
| Read contact-form messages | Contact Messages |
| Change site title, SEO description, footer text | Website Settings |
| Change any button/label text in English or Bangla | Language Content |
| Change your admin email or password | Account Settings |

Every list has: **Active/Hidden** button (show/hide on the site), **up/down arrows** (reorder), **Edit**, **Delete**.

**Important - please review the seeded text.** Only facts you supplied were added (name, university, department, projects, volunteer organization names, social links, skill names). Descriptions are short generic placeholders. **Skill levels are placeholder numbers** - set your real levels. No dates, roles, awards or experience were invented: add them in the dashboard.

## 14. Run the automated tests (recommended)
```powershell
php artisan test
```
Tests use an in-memory SQLite database (PHP extension `pdo_sqlite`, usually enabled) and do not touch your MySQL data. Image-upload tests need the `gd` extension. Please send me any failing test output so it can be fixed.

## 15. Deploying later
1. Get hosting with PHP 8.2+, MySQL, SSH/Composer (VPS or cPanel hosting with "Setup PHP App").
2. Upload the project (without `.env`, `vendor`). Point the domain's **document root to the `public` folder**.
3. On the server: `composer install --no-dev --optimize-autoloader`, copy `.env.example` to `.env` and set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`, database details.
4. `php artisan key:generate`, `php artisan migrate --force --seed`, `php artisan storage:link`, `php artisan portfolio:create-admin`.
5. Optional speed-up: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
6. Use HTTPS and set `SESSION_SECURE_COOKIE=true`. Make `storage` and `bootstrap/cache` writable.

## Troubleshooting
- **"php is not recognized"** - add PHP to PATH (see Software), open a new terminal.
- **Images not showing** - run `php artisan storage:link` (step 9) and make sure `APP_URL` is not pointing to a different port than you use.
- **SQLSTATE connection refused / unknown database** - WAMP must be running; check the `DB_*` lines in `.env`; the database must exist.
- **419 Page Expired** - refresh the page and try again (session expired).
- **Upload fails for big files** - raise `upload_max_filesize` and `post_max_size` in php.ini (project limits: images 4 MB, certificates 8 MB, CV 5 MB).
- After editing `.env`, run `php artisan config:clear`.

## Security summary
Password hashing (bcrypt), session regeneration on login, CSRF on all forms, login rate limiting, contact-form rate limit + honeypot, admin-only middleware on every `/admin` route, validated uploads (type + size, random server-generated filenames, SVG not allowed), Eloquent queries only, no public admin registration, no passwords in code.

## Project structure (short)
`app/Http/Controllers` (public + `Admin/`) - `app/Models` - `config/admin.php` (defines the admin forms) - `database/migrations`, `database/seeders` - `lang/en`, `lang/bn` (all UI text) - `resources/views` - `routes/web.php` - `tests/Feature`.
