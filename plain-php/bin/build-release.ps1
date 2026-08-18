[CmdletBinding()]
param([string]$Version = (git rev-parse --short HEAD))
$ErrorActionPreference='Stop'
$repo=(Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$source=Join-Path $repo 'plain-php'
$releaseRoot=Join-Path $repo 'build\releases'
$stage=Join-Path $releaseRoot ("ishep-preview-$Version")
$zip="$stage.zip"
if(Test-Path $stage){Remove-Item -LiteralPath $stage -Recurse -Force}
if(Test-Path $zip){Remove-Item -LiteralPath $zip -Force}
New-Item -ItemType Directory -Force -Path $stage | Out-Null
foreach($dir in @('public','src','templates','config','database','bin')){Copy-Item -LiteralPath (Join-Path $source $dir) -Destination $stage -Recurse}
foreach($file in @('.env.example','composer.json')){Copy-Item -LiteralPath (Join-Path $source $file) -Destination $stage}
foreach($developmentTool in @('bin\serve.php','bin\build-release.ps1')){$path=Join-Path $stage $developmentTool;if(Test-Path $path){Remove-Item -LiteralPath $path -Force}}
if(Test-Path (Join-Path $source 'vendor')){Copy-Item -LiteralPath (Join-Path $source 'vendor') -Destination $stage -Recurse}
foreach($dir in @('storage\logs','storage\sessions','storage\private\documents')){$target=Join-Path $stage $dir;New-Item -ItemType Directory -Force -Path $target | Out-Null;Set-Content -LiteralPath (Join-Path $target '.gitignore') -Value "*`n!.gitignore`n"}
Copy-Item -LiteralPath (Join-Path $repo 'docs\DIRECTADMIN_STAGING_DEPLOYMENT.md') -Destination $stage
$forbidden=Get-ChildItem -LiteralPath $stage -Recurse -Force | Where-Object {$_.Name -eq '.env' -or $_.FullName -match '\\storage\\(logs|sessions|private\\documents)\\(?!\.gitignore$).+'}
if($forbidden){throw 'Release contains forbidden private/runtime files.'}
foreach($required in @('public\index.php','public\.htaccess','src','templates','config','database\install.sql','bin\user-admin.php','.env.example','storage\private\documents')){if(-not(Test-Path (Join-Path $stage $required))){throw "Missing required release path: $required"}}
Compress-Archive -Path (Join-Path $stage '*') -DestinationPath $zip -CompressionLevel Optimal
$hash=(Get-FileHash -LiteralPath $zip -Algorithm SHA256).Hash
Set-Content -LiteralPath "$zip.sha256" -Value "$hash  $(Split-Path $zip -Leaf)"
Write-Output "Release: $zip";Write-Output "SHA256: $hash"
