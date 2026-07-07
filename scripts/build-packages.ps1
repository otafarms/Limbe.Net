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

	New-ZipFromDirectory -SourceDir $SourceDir -DestinationZip $DestinationZip
}

function New-FolderZip {
	param(
		[string] $SourceDir,
		[string] $DestinationZip
	)

	if (Test-Path -LiteralPath $DestinationZip) {
		Remove-Item -LiteralPath $DestinationZip -Force
	}

	New-ZipFromDirectory -SourceDir $SourceDir -DestinationZip $DestinationZip -EntryPrefix (Split-Path -Leaf $SourceDir)
}

function New-ZipFromDirectory {
	param(
		[string] $SourceDir,
		[string] $DestinationZip,
		[string] $EntryPrefix = ''
	)

	Add-Type -AssemblyName System.IO.Compression
	Add-Type -AssemblyName System.IO.Compression.FileSystem

	$sourceRoot = (Resolve-Path $SourceDir).Path.TrimEnd('\', '/')
	$destinationStream = [System.IO.File]::Open($DestinationZip, [System.IO.FileMode]::CreateNew)
	$archive = New-Object System.IO.Compression.ZipArchive($destinationStream, [System.IO.Compression.ZipArchiveMode]::Create)

	try {
		Get-ChildItem -LiteralPath $sourceRoot -Recurse -File -Force | ForEach-Object {
			$relativePath = $_.FullName.Substring($sourceRoot.Length).TrimStart('\', '/')
			$entryName = $relativePath.Replace('\', '/')

			if ($EntryPrefix) {
				$entryName = "$EntryPrefix/$entryName"
			}

			[System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
				$archive,
				$_.FullName,
				$entryName,
				[System.IO.Compression.CompressionLevel]::Optimal
			) | Out-Null
		}
	}
	finally {
		$archive.Dispose()
		$destinationStream.Dispose()
	}
}

$pluginDir = Join-Path $repoRoot 'wp-content/plugins/limbenet-core'
$pluginZip = Join-Path $distDir 'limbenet-core.zip'

# Keep the plugin package flat and use forward-slash zip paths so WordPress
# places limbenet-core.php directly inside the upload destination on Linux hosts.
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
		New-FlatZip -SourceDir $themeDir -DestinationZip $themeZip
	}

	Copy-Item -LiteralPath (Join-Path $distDir 'limbenet-coastwave.zip') -Destination (Join-Path $distDir 'limbenet-coastwave-fresh.zip') -Force
}

Write-Output "Built $pluginZip"
if (-not $PluginOnly) {
	Write-Output "Built theme packages in $distDir"
}
