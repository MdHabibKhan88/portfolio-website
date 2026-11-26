<#
  setup_windows.ps1 - one-step setup for the portfolio website (Windows 10/11, PowerShell 5.1+)

  Run from the project folder:
      powershell -ExecutionPolicy Bypass -File .\setup_windows.ps1

  What it does: checks PHP + Composer, installs dependencies, creates .env (never overwrites an
  existing one), asks for database settings, generates the app key, runs migrations + seeders,
  creates the storage link and (optionally) creates the admin account.
  You must create the empty MySQL database first (for example in phpMyAdmin).
#>
$ErrorActionPreference = 'Continue'
Set-Location -Path $PSScriptRoot
$warnings = New-Object System.Collections.ArrayList

function Write-Step($m) { Write-Host ""; Write-Host "==> $m" -ForegroundColor Cyan }
function Write-Ok($m)   { Write-Host "[OK] $m" -ForegroundColor Green }
function Write-Warn($m) { Write-Host "[WARNING] $m" -ForegroundColor Yellow; [void]$warnings.Add($m) }
function Stop-Setup($m) { Write-Host "[ERROR] $m" -ForegroundColor Red; Write-Host "Setup stopped. Fix the problem above and run the script again." -ForegroundColor Red; exit 1 }
function Ask-YesNo($q, $default = 'Y') {
    $a = Read-Host "$q [$default]"
    if ([string]::IsNullOrWhiteSpace($a)) { $a = $default }
    return $a.Trim().ToUpper().StartsWith('Y')
}
function ConvertTo-PlainText([System.Security.SecureString]$s) {
    $b = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($s)
    try { return [Runtime.InteropServices.Marshal]::PtrToStringBSTR($b) } finally { [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($b) }
}
function Set-EnvValue([string]$Key, [string]$Value) {
    $path = Join-Path $PSScriptRoot '.env'
    $safe = $Value
    if ($Value -match '[\s#"''$\\]') {
        $safe = $Value.Replace('\', '\\').Replace('"', '\"').Replace('$', '\$')
        $safe = '"' + $safe + '"'
    }
    $lines = [System.IO.File]::ReadAllLines($path)
    $found = $false
    $out = foreach ($l in $lines) {
        if ($l -match ('^\s*' + [regex]::Escape($Key) + '=')) { $found = $true; "$Key=$safe" } else { $l }
    }
    if (-not $found) { $out = @($out) + "$Key=$safe" }
    # UTF-8 WITHOUT BOM (a BOM would break the first .env line)
    [System.IO.File]::WriteAllLines($path, [string[]]$out, (New-Object System.Text.UTF8Encoding($false)))
}

# ---------- 1. PHP ----------
Write-Step 'Checking PHP'
if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Stop-Setup "PHP was not found. If you use WAMP, add its PHP folder (for example C:\wamp64\bin\php\php8.3.x) to the Windows PATH, then open a NEW PowerShell window."
}
$phpVersion = (& php -r 'echo PHP_VERSION;')
if ($LASTEXITCODE -ne 0 -or -not $phpVersion) { Stop-Setup 'PHP was found but could not run.' }
$v = [version](($phpVersion -replace '[^0-9.].*$', ''))
if ($v -lt [version]'8.2.0') { Stop-Setup "PHP $phpVersion found, but PHP 8.2 or newer is required." }
Write-Ok "PHP $phpVersion"
$mods = (& php -m) | ForEach-Object { $_.Trim().ToLower() }
$missing = @('mbstring', 'openssl', 'pdo_mysql', 'fileinfo', 'ctype', 'tokenizer', 'xml') | Where-Object { $mods -notcontains $_ }
if ($missing) { Stop-Setup ("These PHP extensions are not enabled: " + ($missing -join ', ') + ". Enable them in php.ini (WAMP: tray icon > PHP > PHP extensions) - note the command-line php.ini can differ from the Apache one.") }
if ($mods -notcontains 'gd') { Write-Warn 'The PHP "gd" extension is not enabled. The website works, but the automated image-upload tests need it.' }
Write-Ok 'Required PHP extensions are enabled'

# ---------- 2. Composer ----------
Write-Step 'Checking Composer'
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    Stop-Setup 'Composer was not found. Install it from https://getcomposer.org/download/ and open a NEW PowerShell window.'
}
Write-Ok 'Composer found'

Write-Step 'Installing PHP dependencies (needs internet, may take a few minutes)'
& composer install --no-interaction --prefer-dist
if ($LASTEXITCODE -ne 0) { Stop-Setup 'composer install failed. Check your internet connection and the messages above.' }
Write-Ok 'Dependencies installed'

# ---------- 3. .env ----------
Write-Step 'Preparing the .env file'
$envPath = Join-Path $PSScriptRoot '.env'
$configureDb = $true
if (Test-Path $envPath) {
    Write-Host 'An existing .env file was found. It will NOT be overwritten.' -ForegroundColor Yellow
    $configureDb = Ask-YesNo 'Do you want to update only its database settings?' 'N'
} else {
    Copy-Item (Join-Path $PSScriptRoot '.env.example') $envPath
    Write-Ok '.env created from .env.example'
}

if ($configureDb) {
    Write-Host ''
    Write-Host 'Database settings. Create an EMPTY MySQL database first (for example in phpMyAdmin).' -ForegroundColor Cyan
    if (-not (Ask-YesNo 'Have you already created the database?' 'N')) {
        Stop-Setup 'Please create the database (phpMyAdmin > Databases > Create, collation utf8mb4_unicode_ci), then run this script again.'
    }
    $dbHost = Read-Host 'Database host [127.0.0.1]';      if ([string]::IsNullOrWhiteSpace($dbHost)) { $dbHost = '127.0.0.1' }
    $dbPort = Read-Host 'Database port [3306]';           if ([string]::IsNullOrWhiteSpace($dbPort)) { $dbPort = '3306' }
    $dbName = Read-Host 'Database name [portfolio_db]';   if ([string]::IsNullOrWhiteSpace($dbName)) { $dbName = 'portfolio_db' }
    $dbUser = Read-Host 'Database username [root]';       if ([string]::IsNullOrWhiteSpace($dbUser)) { $dbUser = 'root' }
    $dbPass = ConvertTo-PlainText (Read-Host 'Database password (press Enter if empty)' -AsSecureString)
    Set-EnvValue 'DB_CONNECTION' 'mysql'
    Set-EnvValue 'DB_HOST' $dbHost
    Set-EnvValue 'DB_PORT' $dbPort
    Set-EnvValue 'DB_DATABASE' $dbName
    Set-EnvValue 'DB_USERNAME' $dbUser
    Set-EnvValue 'DB_PASSWORD' $dbPass
    Write-Ok 'Database settings saved to .env'
}

# ---------- 4. App key ----------
Write-Step 'Application key'
$envText = [System.IO.File]::ReadAllText($envPath)
if ($envText -match '(?m)^APP_KEY=\s*$' -or $envText -notmatch '(?m)^APP_KEY=') {
    & php artisan key:generate --force
    if ($LASTEXITCODE -ne 0) { Stop-Setup 'Could not generate the application key.' }
    Write-Ok 'Application key generated'
} else {
    Write-Ok 'Application key already set (kept)'
}

# ---------- 5. Migrations + seeders ----------
Write-Step 'Creating database tables and initial content'
& php artisan migrate --force
if ($LASTEXITCODE -ne 0) { Stop-Setup 'Migrations failed. Check that MySQL is running and the database name/user/password in .env are correct.' }
Write-Ok 'Migrations finished'
& php artisan db:seed --force
if ($LASTEXITCODE -ne 0) { Stop-Setup 'Seeding failed. See the message above.' }
Write-Ok 'Initial content added'

# ---------- 6. Storage link ----------
Write-Step 'Creating the storage link (for uploaded images/files)'
if (Test-Path (Join-Path $PSScriptRoot 'public\storage')) {
    Write-Ok 'public\storage already exists'
} else {
    & php artisan storage:link
    if ($LASTEXITCODE -ne 0) {
        Write-Warn 'storage:link failed, so uploaded images will not show. Run this script (or "php artisan storage:link") in a PowerShell opened with "Run as administrator", or enable Windows Developer Mode.'
    } else { Write-Ok 'Storage link created' }
}

# ---------- 7. Admin account ----------
Write-Step 'Admin account'
if (Ask-YesNo 'Create (or reset) the admin account now?' 'Y') {
    $adminEmail = Read-Host 'Admin email'
    $adminName  = Read-Host 'Admin name [MD. HABIB KHAN]'; if ([string]::IsNullOrWhiteSpace($adminName)) { $adminName = 'MD. HABIB KHAN' }
    $p1 = ConvertTo-PlainText (Read-Host 'Admin password (min 10 characters, letters AND numbers)' -AsSecureString)
    $p2 = ConvertTo-PlainText (Read-Host 'Repeat password' -AsSecureString)
    if ($p1 -ne $p2) {
        Write-Warn 'The two passwords did not match. Admin not created. Run: php artisan portfolio:create-admin'
    } else {
        $env:ADMIN_EMAIL = $adminEmail; $env:ADMIN_NAME = $adminName; $env:ADMIN_PASSWORD = $p1
        & php artisan portfolio:create-admin --no-interaction
        $code = $LASTEXITCODE
        Remove-Item Env:\ADMIN_EMAIL, Env:\ADMIN_NAME, Env:\ADMIN_PASSWORD -ErrorAction SilentlyContinue
        if ($code -ne 0) { Write-Warn 'Admin account was NOT created (see the message above). Run: php artisan portfolio:create-admin' }
        else { Write-Ok 'Admin account ready' }
    }
} else {
    Write-Warn 'No admin account yet. Create one later with: php artisan portfolio:create-admin'
}

# ---------- Summary ----------
Write-Host ''
Write-Host '=========================================' -ForegroundColor Green
Write-Host ' Setup finished' -ForegroundColor Green
Write-Host '=========================================' -ForegroundColor Green
if ($warnings.Count -gt 0) {
    Write-Host 'Things that need your attention:' -ForegroundColor Yellow
    $warnings | ForEach-Object { Write-Host " - $_" -ForegroundColor Yellow }
}
Write-Host 'Start the website with:  php artisan serve'
Write-Host 'Website:      http://localhost:8000'
Write-Host 'Admin login:  http://localhost:8000/admin/login'
if (Ask-YesNo 'Start the website now?' 'Y') { & php artisan serve }
