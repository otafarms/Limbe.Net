param(
	[switch] $PluginOnly
)

$ErrorActionPreference = 'Stop'

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$distDir = Join-Path $repoRoot 'dist'

if (-not (Test-Path -LiteralPath $distDir)) {
	New-Item -ItemType Directory -Path $distDir | Out-Null
}

function New-FlatZip {
	param(
		[string] $SourceDir,
		[string] $DestinationZip
	)

	if (Test-Path -LiteralPath $DestinationZip) {
		Remove-Item -LiteralPath $DestinationZip -Force
	}

	$sourceItems = Join-Path $SourceDir '*'
	Compress-Archive -Path $sourceItems -DestinationPath $DestinationZip -Force
}

function New-FolderZip {
	param(
		[string] $SourceDir,
		[string] $DestinationZip
	)

	if (Test-Path -LiteralPath $DestinationZip) {
		Remove-Item -LiteralPath $DestinationZip -Force
	}

	Compress-Archive -Path $SourceDir -DestinationPath $DestinationZip -Force
}

$pluginDir = Join-Path $repoRoot 'wp-content/plugins/limbenet-core'
$pluginZip = Join-Path $distDir 'limbenet-core.zip'

# Keep the plugin package flat so WordPress places limbenet-core.php directly
# inside the upload destination, even if the uploaded file is renamed locally.
New-FlatZip -SourceDir $pluginDir -DestinationZip $pluginZip

if (-not $PluginOnly) {
	$themes = @(
		'limbenet',
		'limbenet-coastwave',
		'limbenet-festivaltrail'
	)

	foreach ($theme in $themes) {
		$themeDir = Join-Path $repoRoot "wp-content/themes/$theme"
		$themeZip = Join-Path $distDir "$theme.zip"
		New-FolderZip -SourceDir $themeDir -DestinationZip $themeZip
	}
}

Write-Output "Built $pluginZip"
if (-not $PluginOnly) {
	Write-Output "Built theme packages in $distDir"
}
