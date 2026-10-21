$ErrorActionPreference = 'Stop'

$apacheRoot = 'C:\wamp64\bin\apache\apache2.4.65'
$vhostFile = Join-Path $apacheRoot 'conf\extra\httpd-vhosts.conf'
$httpd = Join-Path $apacheRoot 'bin\httpd.exe'
$frontend = 'c:/wamp64/www/compracompu/frontend'
$api = 'c:/wamp64/www/compracompu/api/public'

if (-not (Test-Path -LiteralPath $vhostFile) -or -not (Test-Path -LiteralPath $httpd)) {
    throw 'No se encontró la configuración de Apache de WAMP en C:\wamp64.'
}

$original = [System.IO.File]::ReadAllText($vhostFile)
$defaultVhost = @"
<VirtualHost _default_:80>
  ServerName localhost
  ServerAlias localhost
  DocumentRoot "$frontend"
  Alias "/api" "$api"
  <Directory "$frontend/">
    Options +Indexes +Includes +FollowSymLinks +MultiViews
    AllowOverride All
    Require all granted
  </Directory>
  <Directory "$api/">
    Options +Indexes +Includes +FollowSymLinks +MultiViews
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
"@
$updated = [regex]::Replace($original, '(?s)<VirtualHost _default_:80>.*?</VirtualHost>', $defaultVhost, 1)
if ($updated -eq $original) { throw 'No se encontró el VirtualHost predeterminado; no se modificó Apache.' }

$wwwPattern = '(?s)<VirtualHost \*:80>\s*ServerName www\.2026compra\.com.*?</VirtualHost>'
$wwwMatch = [regex]::Match($updated, $wwwPattern)
if ($wwwMatch.Success) {
    $wwwVhost = $wwwMatch.Value -replace 'Require local', 'Require all granted'
    if ($wwwVhost -notmatch 'Alias "/api"') {
        $wwwVhost = [regex]::Replace($wwwVhost, '(DocumentRoot "[^"]+"\s*)', ('$1' + "`r`n`tAlias `"/api`" `"$api`""), 1)
    }
    if ($wwwVhost -notmatch [regex]::Escape("<Directory `"$api/`">") ) {
        $apiDirectory = "  <Directory `"$api/`">`r`n    Options +Indexes +Includes +FollowSymLinks +MultiViews`r`n    AllowOverride All`r`n    Require all granted`r`n  </Directory>`r`n"
        $wwwVhost = $wwwVhost.Replace('</VirtualHost>', $apiDirectory + '</VirtualHost>')
    }
    $updated = $updated.Replace($wwwMatch.Value, $wwwVhost)
} else {
    throw 'No se encontró el VirtualHost www.2026compra.com.'
}

$backup = "$vhostFile.compraCompu.bak"
if (-not (Test-Path -LiteralPath $backup)) {
    [System.IO.File]::Copy($vhostFile, $backup)
}
[System.IO.File]::WriteAllText($vhostFile, $updated, [System.Text.UTF8Encoding]::new($false))

& $httpd -t
if ($LASTEXITCODE -ne 0) {
    [System.IO.File]::Copy($backup, $vhostFile, $true)
    throw 'Apache rechazó la configuración; se restauró el archivo original.'
}

$ruleName = 'CompraCompu WAMP HTTP (red local)'
if (-not (Get-NetFirewallRule -DisplayName $ruleName -ErrorAction SilentlyContinue)) {
    New-NetFirewallRule -DisplayName $ruleName -Direction Inbound -Program $httpd -Action Allow -Protocol TCP -LocalPort 80 -RemoteAddress LocalSubnet -Profile Any | Out-Null
}
Restart-Service -Name 'wampapache64' -Force
Write-Output 'Configuración aplicada. Desde otro dispositivo en la misma red, abre http://192.168.4.107'
