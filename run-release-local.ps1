# =============================================================================
#  Runs the built release (dist\bedo-factory-*.tar.gz) on this laptop, the way
#  it runs on the Raspberry Pi: production mode, only the files in the archive.
#
#      powershell -ExecutionPolicy Bypass -File .\run-release-local.ps1
#
#  Then open http://127.0.0.1:8080   (Ctrl + C to stop)
#
#  The broker address and MQTT password are taken from this project's .env,
#  because on the laptop the page can't use "the machine that served it".
# =============================================================================
$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot

$archive = Get-ChildItem 'dist\bedo-factory-*.tar.gz' -ErrorAction SilentlyContinue |
    Sort-Object LastWriteTime -Descending | Select-Object -First 1
if (-not $archive) { throw 'No release found. Run .\build-release.ps1 first.' }

$testDir = Join-Path $PSScriptRoot 'dist\local-test'
$app     = Join-Path $testDir 'bedo-factory'
$port    = 8080

Write-Host "==> Release: $($archive.Name)"
if (Test-Path $testDir) { Remove-Item $testDir -Recurse -Force }
New-Item -ItemType Directory -Force $testDir | Out-Null
tar -xzf $archive.FullName -C $testDir
if ($LASTEXITCODE -ne 0) { throw 'Could not extract the release.' }

# MQTT settings from the project .env (broker IP + password of user "web")
$devEnv = Get-Content '.env' -Raw
function EnvValue($key) {
    $m = [regex]::Match($devEnv, "(?m)^$key=(.*)$")
    if ($m.Success) { $m.Groups[1].Value.Trim() } else { '' }
}
$wsUrl = EnvValue 'MQTT_WS_URL'
if (-not $wsUrl) { throw 'Set MQTT_WS_URL in .env (e.g. ws://172.20.151.6:9001).' }

$envFile = Get-Content "$app\deploy\.env.production" -Raw
$envFile = $envFile -replace '(?m)^MQTT_WS_URL=.*$',   "MQTT_WS_URL=$wsUrl"
$envFile = $envFile -replace '(?m)^MQTT_USERNAME=.*$', "MQTT_USERNAME=$(EnvValue 'MQTT_USERNAME')"
$envFile = $envFile -replace '(?m)^MQTT_PASSWORD=.*$', "MQTT_PASSWORD=$(EnvValue 'MQTT_PASSWORD')"
[IO.File]::WriteAllText("$app\.env", $envFile, (New-Object Text.UTF8Encoding $false))

Push-Location $app
try {
    php artisan key:generate --force --no-interaction | Out-Null
    php artisan optimize --no-interaction | Out-Null
    Write-Host "==> Broker: $wsUrl"
    Write-Host "==> Open:   http://127.0.0.1:$port   (Ctrl + C to stop)"
    Write-Host ""
    # artisan serve uses Laravel's router: real files (build/*.css, *.js,
    # images) are sent as files, everything else goes to Laravel – like nginx.
    php artisan serve --host=127.0.0.1 --port=$port --no-reload
}
finally {
    Pop-Location
}
