# =============================================================================
#  Builds a release of the dashboard for the Raspberry Pi.
#
#      powershell -ExecutionPolicy Bypass -File .\build-release.ps1
#
#  Output: dist\bedo-factory-<version>.tar.gz
#  It contains the bundled front-end (no internet needed), production PHP
#  packages (vendor/, no dev tools) and the Pi install script.
#  Not included: .env, tests, node_modules, flutter, logs.
# =============================================================================
$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot

function Run($exe, [string[]]$arguments) {
    & $exe @arguments
    if ($LASTEXITCODE -ne 0) { throw "$exe $($arguments -join ' ') failed (exit $LASTEXITCODE)" }
}

# Portable PHP for the Windows zip (official build from windows.php.net).
$phpBranch = '8.4'
$phpKeepExtensions = 'openssl', 'mbstring', 'fileinfo', 'curl'

# Downloads (cached in dist\cache) and unpacks a trimmed, pre-configured PHP into $target.
function Add-PortablePhp([string]$target) {
    $base = 'https://downloads.php.net/~windows/releases'
    $release = (Invoke-RestMethod "$base/releases.json" -UseBasicParsing).$phpBranch
    $build = $release.PSObject.Properties | Where-Object { $_.Name -like 'nts-*-x64' } | Select-Object -First 1
    if (-not $build) { throw "No x64 NTS build of PHP $phpBranch found on windows.php.net" }
    $file = $build.Value.zip.path

    $cache = Join-Path $dist 'cache'
    New-Item -ItemType Directory -Force $cache | Out-Null
    $download = Join-Path $cache $file
    if (-not (Test-Path $download)) {
        Write-Host "==> Downloading PHP $($release.version) ($file)"
        $ProgressPreference = 'SilentlyContinue'
        try {
            Invoke-WebRequest "$base/$file" -OutFile $download -UseBasicParsing
        } catch {
            Remove-Item $download -Force -ErrorAction SilentlyContinue
            throw
        }
    }
    $hash = (Get-FileHash $download -Algorithm SHA256).Hash
    if ($hash -ne $build.Value.zip.sha256.ToUpper()) {
        Remove-Item $download -Force
        throw "PHP download checksum mismatch - deleted, run the build again."
    }

    Expand-Archive $download -DestinationPath $target -Force

    # Trim: only php.exe and the extensions Laravel needs here.
    Remove-Item "$target\dev", "$target\extras", "$target\lib", "$target\phpdbg.exe", "$target\php-cgi.exe", "$target\*.lib" `
        -Recurse -Force -ErrorAction SilentlyContinue
    Get-ChildItem "$target\ext" -Filter 'php_*.dll' |
        Where-Object { $phpKeepExtensions -notcontains ($_.BaseName -replace '^php_', '') } |
        Remove-Item -Force
    # Libraries only used by the removed extensions (intl, enchant, pgsql, sqlite, sodium …)
    # and tools we don't need. curl keeps libssl/libcrypto/libssh2/nghttp2/brotli.
    foreach ($pattern in 'icu*.dll', 'libenchant*.dll', 'glib-*.dll', 'gmodule-*.dll', 'gobject-*.dll',
                         'libpq.dll', 'libsqlite3.dll', 'libsodium.dll', 'php8phpdbg.dll',
                         'deplister.exe', 'php-win.exe', 'phar.phar.bat', 'pharcommand.phar', 'news.txt') {
        Remove-Item (Join-Path $target $pattern) -Force -ErrorAction SilentlyContinue
    }

    # php.ini next to php.exe (extension_dir is set to an absolute path by start-windows.ps1).
    $ini = Get-Content "$target\php.ini-production" -Raw
    $ini = $ini -replace '(?m)^;\s*extension_dir = "ext"\s*$', 'extension_dir = "ext"'
    foreach ($ext in $phpKeepExtensions) { $ini = $ini -replace "(?m)^;(extension=$ext)\s*$", '$1' }
    $ini = $ini -replace '(?m)^memory_limit = .*$', 'memory_limit = 256M'
    [IO.File]::WriteAllText("$target\php.ini", $ini, (New-Object Text.UTF8Encoding $false))
    Remove-Item "$target\php.ini-*" -Force

    # Visual C++ runtime next to php.exe, so laptops without it still work.
    foreach ($dll in 'vcruntime140.dll', 'vcruntime140_1.dll') {
        $source = Join-Path $env:WINDIR "System32\$dll"
        if (Test-Path $source) { Copy-Item $source $target -Force }
        else { Write-Warning "$dll not found on this PC - laptops may need the VC++ Redistributable." }
    }

    # Check with the real php.ini (its relative extension_dir works from inside $target).
    Push-Location $target
    try {
        # No double quotes inside -r: Windows PowerShell 5.1 strips them.
        $check = & .\php.exe -c php.ini -r 'echo PHP_VERSION, chr(32), implode(chr(44), get_loaded_extensions());' 2>&1
    } finally { Pop-Location }
    $loaded = ($check -split ' ', 2)[1] -split ','
    $missing = $phpKeepExtensions | Where-Object { $loaded -notcontains $_ }
    if ($LASTEXITCODE -ne 0 -or $missing) { throw "Bundled PHP check failed ($check). Missing: $missing" }
    Write-Host "==> Bundled PHP $(($check -split ' ')[0]) (+ $($phpKeepExtensions -join ', '))"
}

$version = ([regex]::Match((Get-Content 'config\factory.php' -Raw), "'version'\s*=>\s*'([^']+)'")).Groups[1].Value
if (-not $version) { throw "Could not read 'version' from config\factory.php" }
$name    = 'bedo-factory'
$dist    = Join-Path $PSScriptRoot 'dist'
# Staged in a fresh temp folder: a folder left open in the editor can't block the build.
$stageRoot = Join-Path $env:TEMP "bedo-build-$(Get-Random)"
$stage   = Join-Path $stageRoot $name
$archive = Join-Path $dist "$name-$version.tar.gz"
New-Item -ItemType Directory -Force $dist | Out-Null

Write-Host "==> Building $name $version"

# 1. Front-end bundle (Tailwind, Alpine, mqtt.js, Inter font) -> public/build
Run npm @('ci', '--no-audit', '--no-fund')
Run npm @('run', 'build')

# 2. Fresh staging folder with only what the server needs
New-Item -ItemType Directory -Force $stage | Out-Null

foreach ($item in 'app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'deploy', 'artisan', 'composer.json', 'composer.lock') {
    Copy-Item $item -Destination $stage -Recurse -Force
}
# Remove local-only files from the copy
Remove-Item "$stage\bootstrap\cache\*.php" -Force -ErrorAction SilentlyContinue
Remove-Item "$stage\database\*.sqlite" -Force -ErrorAction SilentlyContinue
Remove-Item "$stage\public\hot" -Force -ErrorAction SilentlyContinue
Remove-Item "$stage\public\storage" -Force -Recurse -ErrorAction SilentlyContinue
# Front-end sources are already bundled into public/build
Remove-Item "$stage\resources\js", "$stage\resources\css" -Recurse -Force

foreach ($dir in 'app\public', 'framework\cache\data', 'framework\sessions', 'framework\views', 'logs') {
    New-Item -ItemType Directory -Force "$stage\storage\$dir" | Out-Null
}

# 3. Production PHP packages only
Run composer @('install', '--no-dev', '--optimize-autoloader', '--no-interaction', '--no-progress', "--working-dir=$stage")
Remove-Item "$stage\bootstrap\cache\*.php" -Force -ErrorAction SilentlyContinue

# 4. Shell scripts must have Linux line endings
Get-ChildItem "$stage\deploy" -Include *.sh, *.conf, .env.production -Recurse -Force | ForEach-Object {
    $text = [IO.File]::ReadAllText($_.FullName) -replace "`r`n", "`n"
    [IO.File]::WriteAllText($_.FullName, $text, (New-Object Text.UTF8Encoding $false))
}

# 5. tar.gz for the Pi (no Windows files)
$zip = Join-Path $dist "$name-$version.zip"
foreach ($file in $archive, $zip) { if (Test-Path $file) { Remove-Item $file -Force } }
Run tar @('-czf', $archive, '-C', $stageRoot, $name)

# 6. Windows zip: + portable PHP + double-click launcher (nothing to install)
Add-PortablePhp (Join-Path $stage 'php')
Copy-Item 'deploy\start-windows.bat' -Destination $stage -Force
Run tar @('-a', '-cf', $zip, '-C', $stageRoot, $name)
Remove-Item $stageRoot -Recurse -Force -ErrorAction SilentlyContinue

Write-Host ""
Write-Host ("==> Raspberry Pi: $archive ({0:N1} MB)" -f ((Get-Item $archive).Length / 1MB))
Write-Host ("==> Windows:      $zip ({0:N1} MB)" -f ((Get-Item $zip).Length / 1MB))
Write-Host ""
Write-Host "Windows laptop: unzip, double-click bedo-factory\start-windows.bat (PHP is included)."
Write-Host ""
Write-Host "Copy it to the Pi and install:"
Write-Host "    scp `"$archive`" pi@172.20.151.6:~"
Write-Host "    ssh pi@172.20.151.6"
Write-Host "    tar -xzf $name-$version.tar.gz && cd $name && sudo bash deploy/install-on-pi.sh"
