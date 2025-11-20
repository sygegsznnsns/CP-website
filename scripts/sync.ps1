Param(
  [string]$ConfigPath = "$PSScriptRoot/config.json",
  [ValidateSet('copy','link')][string]$Mode = 'copy',
  [switch]$DryRun
)

function Get-SitePath {
  param([string]$configPath)
  if (Test-Path $configPath) {
    try {
      $json = Get-Content -Raw -Path $configPath | ConvertFrom-Json
      if ($null -eq $json.sitePath -or $json.sitePath -eq '') {
        throw "config.json 缺少 sitePath 字段"
      }
      return $json.sitePath
    } catch {
      throw "读取配置失败: $_"
    }
  } else {
    throw "未找到配置文件: $configPath。请复制 config.example.json → config.json 并填写 sitePath"
  }
}

function Sync-Dir {
  param(
    [string]$src,
    [string]$dest,
    [string]$mode,
    [switch]$dryRun
  )
  if (!(Test-Path $src)) { throw "源目录不存在: $src" }
  $destParent = Split-Path -Parent $dest
  if (!(Test-Path $destParent)) { New-Item -ItemType Directory -Force -Path $destParent | Out-Null }

  if ($mode -eq 'link') {
    if ($dryRun) { Write-Host "将创建符号链接: $dest -> $src" -ForegroundColor Yellow; return }
    if (Test-Path $dest) { Remove-Item -Recurse -Force -Path $dest }
    try {
      New-Item -ItemType SymbolicLink -Path $dest -Target $src -ErrorAction Stop | Out-Null
      Write-Host "已创建符号链接: $dest -> $src" -ForegroundColor Green
    } catch {
      Write-Warning "创建符号链接失败，尝试创建目录联接 (需要管理员或开发者模式): $_"
      cmd /c "mklink /J `"$dest`" `"$src`"" | Out-Null
      Write-Host "已创建目录联接: $dest -> $src" -ForegroundColor Green
    }
  } else {
    if ($dryRun) { Write-Host "将镜像复制: $src -> $dest" -ForegroundColor Yellow; return }
    if (!(Test-Path $dest)) { New-Item -ItemType Directory -Force -Path $dest | Out-Null }
    $robocopy = Get-Command robocopy -ErrorAction SilentlyContinue
    if ($robocopy) {
      & robocopy $src $dest /MIR /Z /XD ".git" "node_modules" "vendor" | Out-Null
      Write-Host "已镜像复制: $src -> $dest" -ForegroundColor Green
    } else {
      Copy-Item -Path (Join-Path $src '*') -Destination $dest -Recurse -Force
      Write-Host "已复制(非镜像): $src -> $dest" -ForegroundColor Green
    }
  }
}

try {
  $sitePath = Get-SitePath -configPath $ConfigPath
  $srcRoot = Resolve-Path (Join-Path $PSScriptRoot '..\src\wp-content')
  $srcTheme = Join-Path $srcRoot 'themes\musicalbum-child'
  $srcPlugin = Join-Path $srcRoot 'plugins\musicalbum-integrations'

  $destTheme = Join-Path $sitePath 'wp-content\themes\musicalbum-child'
  $destPlugin = Join-Path $sitePath 'wp-content\plugins\musicalbum-integrations'

  Write-Host "模式: $Mode; 站点路径: $sitePath" -ForegroundColor Cyan
  Sync-Dir -src $srcTheme -dest $destTheme -mode $Mode -dryRun:$DryRun
  Sync-Dir -src $srcPlugin -dest $destPlugin -mode $Mode -dryRun:$DryRun
  Write-Host "同步完成" -ForegroundColor Green
} catch {
  Write-Error $_
  exit 1
}