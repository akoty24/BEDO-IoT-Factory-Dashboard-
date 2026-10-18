# =============================================================================
#  BEDO IoT Factory dashboard - run the release on any Windows laptop.
#  Started by start-windows.bat (double-click) in the release folder.
#
#  Needs only PHP 8.2+ (XAMPP or php.net zip). No Node, no Composer:
#  vendor/ and public/build/ are already inside the release.
#  First run asks for the Raspberry Pi address and the MQTT password.
# =============================================================================
$ErrorActionPreference = 'Stop'
$app  = Split-Path $PSScriptRoot -Parent   # release folder (this file is in deploy\)
$port = 8080
Set-Location $app

function Fail($message) {
    Write-Host ""
    Write-Host "ERROR: $message" -ForegroundColor Red
    Read-Host "Press Enter to close"
    exit 1
}

# --- 1. Find PHP ------------------------------------------------------------
# Prefer the PHP bundled in the zip (php\php.exe); otherwise an installed one.
$php = $null
$bundled = Join-Path $app 'php\php.exe'
if (Test-Path $bundled) {
    $php = $bundled
    # php.ini resolves a relative extension_dir from the current folder, so point
    # it at this folder's php\ext every run (the zip can be unpacked anywhere).
    $ini = Join-Path $app 'php\php.ini'
    $iniText = Get-Content $ini -Raw
    $extDir = Join-Path $app 'php\ext'
    $iniText = $iniText -replace '(?m)^extension_dir = .*$', ('extension_dir = "' + $extDir + '"')
    [IO.File]::WriteAllText($ini, $iniText, (New-Object Text.UTF8Encoding $false))
}
if (-not $php) { $php = (Get-Command php -ErrorAction SilentlyContinue).Source }
if (-not $php) {
    foreach ($candidate in 'C:\xampp\php\php.exe', 'C:\php\php.exe', "$env:USERPROFILE\php\php.exe") {
        if (Test-Path $candidate) { $php = $candidate; break }
    }
}
if (-not $php) { Fail "PHP not found. Install XAMPP (https://www.apachefriends.org) or PHP 8.2+, then run again." }

$version = & $php -r 'echo PHP_VERSION;'
# No double quotes inside -r: Windows PowerShell 5.1 strips them from native arguments.
& $php -r 'exit(PHP_VERSION_ID >= 80200 ? 0 : 1);'
if ($LASTEXITCODE -ne 0) { Fail "PHP $version found at $php - Laravel needs PHP 8.2 or newer." }

$missing = @()
foreach ($ext in 'openssl', 'mbstring', 'fileinfo') {
    & $php -r "exit(extension_loaded('$ext') ? 0 : 1);"
    if ($LASTEXITCODE -ne 0) { $missing += $ext }
}
if ($missing) {
    Fail "PHP extensions missing: $($missing -join ', '). Enable them in php.ini (remove the ';' before extension=...)."
}
Write-Host "PHP $version  ($php)"

# --- 2. .env (first run only) -----------------------------------------------
if (-not (Test-Path '.env')) {
    Write-Host ""
    Write-Host "First run - MQTT settings"
    $ip = Read-Host "Raspberry Pi IP address [172.20.151.6]"
    if (-not $ip) { $ip = '172.20.151.6' }

    # Shown while typing on purpose: a hidden password typed with the Arabic
    # keyboard layout is the usual cause of "MQTT login refused".
    Write-Host "Type the password with the keyboard in ENGLISH (Alt + Shift)." -ForegroundColor Yellow
    while ($true) {
        $password = Read-Host "MQTT password of user 'web'"
        & $php artisan mqtt:check-login "--host=$ip" --username=web "--password=$password"
        if ($LASTEXITCODE -eq 0) { break }
        if ($LASTEXITCODE -eq 2) {
            Write-Host "Wrong password - check the keyboard language and try again." -ForegroundColor Red
            continue
        }
        $answer = Read-Host "Raspberry Pi not reachable (same network?). Save these settings anyway? (y/n)"
        if ($answer -eq 'y') { break }
    }

    $envText = Get-Content 'deploy\.env.production' -Raw
    $envText = $envText -replace '(?m)^MQTT_WS_URL=.*$', "MQTT_WS_URL=ws://${ip}:9001"
    $envText = $envText -replace '(?m)^MQTT_PASSWORD=.*$', ('MQTT_PASSWORD="' + ($password -replace '"', '\"') + '"')
    [IO.File]::WriteAllText("$app\.env", $envText, (New-Object Text.UTF8Encoding $false))

    & $php artisan key:generate --force --no-interaction | Out-Null
    Write-Host "Saved to $app\.env  (delete it to enter the settings again)"
}

# --- 3. Caches + server -------------------------------------------------------
& $php artisan optimize --no-interaction | Out-Null
if ($LASTEXITCODE -ne 0) { Fail "php artisan optimize failed - see storage\logs." }

# Every start: warn early if the saved MQTT login no longer works.
& $php artisan mqtt:check-login
if ($LASTEXITCODE -eq 2) {
    Write-Host "The saved MQTT password is refused. Delete $app\.env and start again to re-enter it." -ForegroundColor Red
}

$url = "http://127.0.0.1:$port"
Write-Host ""
Write-Host "Dashboard: $url   (close this window to stop)" -ForegroundColor Green
if (-not $env:BEDO_NO_BROWSER) { Start-Process $url }
& $php artisan serve --host=127.0.0.1 --port=$port --no-reload
