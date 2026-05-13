# Prepara nextline_pyme.sql para importar en Azure MySQL (quita DEFINER que Azure puede rechazar).
# Uso: .\import-to-azure.sql.ps1
# Luego importa: mysql ... nutrinext < nextline_pyme_azure.sql

$in  = "nextline_pyme.sql"
$out = "nextline_pyme_azure.sql"
(Get-Content $in -Raw) -replace 'DEFINER=`[^`]+`@`[^`]+`\s+', '' | Set-Content $out -NoNewline
Write-Host "Creado $out - importa ese archivo en la base nutrinext."
