# Setup BD local para rama feature/gym (WAMP + nextline_pyme)
# Uso: desde la raíz del proyecto, con WAMP/MySQL en verde:
#   powershell -ExecutionPolicy Bypass -File scripts/setup_gym_local.ps1

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

$MysqlExe = "C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe"
if (-not (Test-Path $MysqlExe)) {
    $MysqlExe = Get-ChildItem "C:\wamp64\bin\mysql" -Recurse -Filter "mysql.exe" -ErrorAction SilentlyContinue | Select-Object -First 1 -ExpandProperty FullName
}
if (-not $MysqlExe) {
    Write-Host "No se encontró mysql.exe en WAMP." -ForegroundColor Red
    exit 1
}

# Leer database desde .env
$envFile = Join-Path $Root ".env"
$dbName = "nextline_pyme"
$dbUser = "root"
$dbPass = ""
if (Test-Path $envFile) {
    foreach ($line in Get-Content $envFile) {
        if ($line -match '^\s*database\.default\.database\s*=\s*(.+)$') {
            $dbName = ($Matches[1] -split '#')[0].Trim()
        }
        if ($line -match '^\s*database\.default\.username\s*=\s*(.+)$') {
            $dbUser = ($Matches[1] -split '#')[0].Trim()
        }
        if ($line -match '^\s*database\.default\.password\s*=\s*(.*)$') {
            $dbPass = ($Matches[1] -split '#')[0].Trim()
        }
    }
}

Write-Host "Comprobando MySQL en localhost:3306..." -ForegroundColor Cyan
$tcp = Test-NetConnection -ComputerName localhost -Port 3306 -WarningAction SilentlyContinue
if (-not $tcp.TcpTestSucceeded) {
    Write-Host ""
    Write-Host "MySQL no está escuchando. Inicia WAMP (icono verde -> Start All Services)" -ForegroundColor Yellow
    Write-Host "o ejecuta como administrador: net start wampmysqld64" -ForegroundColor Yellow
    exit 1
}

function Invoke-SqlFile {
    param([string]$Path, [string]$Label)
    if (-not (Test-Path $Path)) {
        Write-Host "  Omitido (no existe): $Path" -ForegroundColor DarkYellow
        return
    }
    Write-Host "  -> $Label" -ForegroundColor Green
    $mysqlArgs = @("-u$dbUser", "-h127.0.0.1", "-P3306")
    if ($dbPass -ne "") { $mysqlArgs += "-p$dbPass" }
    $mysqlArgs += $dbName
    Get-Content -Path $Path -Raw -Encoding UTF8 | & $MysqlExe @mysqlArgs 2>&1 | Out-Host
    if ($LASTEXITCODE -ne 0) { throw "Error ejecutando $Path (exit $LASTEXITCODE)" }
}

Write-Host "Base de datos: $dbName" -ForegroundColor Cyan

Write-Host "`n[1/4] Migraciones CodeIgniter (spark migrate)..." -ForegroundColor Cyan
php spark migrate
if ($LASTEXITCODE -ne 0) { throw "spark migrate falló" }

Write-Host "`n[2/4] Schema Gym (tablas gym_*)..." -ForegroundColor Cyan
Invoke-SqlFile (Join-Path $Root "database\schema_gym.sql") "schema_gym.sql"
Invoke-SqlFile (Join-Path $Root "database\fix_gym_catalog_utf8.sql") "fix_gym_catalog_utf8.sql"

Write-Host "`n[3/4] OAuth Google en usuario..." -ForegroundColor Cyan
$oAuthFile = Join-Path $Root "database\migration_usuario_google_oauth.sql"
if (Test-Path $oAuthFile) {
    try {
        Invoke-SqlFile $oAuthFile "migration_usuario_google_oauth.sql"
    } catch {
        Write-Host "  (puede estar ya aplicado: columnas oauth_*)" -ForegroundColor DarkYellow
    }
}

Write-Host "`n[4/4] Seed módulos y perfil Alumno..." -ForegroundColor Cyan
Invoke-SqlFile (Join-Path $Root "database\seed_gym_modulos_perfiles.sql") "seed_gym_modulos_perfiles.sql"

Write-Host "`nListo. Verifica:" -ForegroundColor Green
Write-Host "  - Dashboard gym: $((Get-Content $envFile | Select-String 'app.baseURL').ToString().Split('=')[1].Trim())dashboard/gym/ejercicio/lista"
Write-Host "  - Web gym:       .../gym"
Write-Host "  - .env: GOOGLE_AUTH_CLIENT_ID, GOOGLE_AUTH_CLIENT_SECRET, GOOGLE_AUTH_REDIRECT_URI (opcional registro Google)"
