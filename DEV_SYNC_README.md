# 开发同步与实时测试说明（Windows）

本项目不将第三方主题/插件纳入仓库。开发与实时测试通过同步/链接 `src` 下的子主题与自研插件到站点 `wp-content` 来实现。

## 准备
- 复制示例配置：将 `scripts/config.example.json` 复制为 `scripts/config.json` 并将 `sitePath` 设置为你的站点根目录（包含 `wp-content`）。
- 安装父主题 Astra（后台：外观 → 主题 → 添加）。
- 在站点后台启用子主题与插件（复制或链接完成后）：
  - 子主题：`Musicalbum Child`
  - 插件：`Musicalbum Integrations`

## 同步脚本使用
- 复制模式（免权限，稳定）：
  - `powershell -ExecutionPolicy Bypass -File .\scripts\sync.ps1 -ConfigPath .\scripts\config.json -Mode copy`
- 链接模式（改动即时生效，需符号链接支持/管理员或开发者模式）：
  - `powershell -ExecutionPolicy Bypass -File .\scripts\sync.ps1 -ConfigPath .\scripts\config.json -Mode link`
- 预演（不做变更）：
  - `powershell -ExecutionPolicy Bypass -File .\scripts\sync.ps1 -ConfigPath .\scripts\config.json -Mode copy -DryRun`

## 开发建议
- 在仓库 `src/wp-content/themes/musicalbum-child/` 中进行模板与样式改动；必要时从 `wp-content/themes/astra/` 复制模板到子主题同路径后修改。
- 在 `src/wp-content/plugins/musicalbum-integrations/` 中集中管理与第三方插件的 hooks、短码与 REST 接口，不要直接修改第三方插件源码。
- 链接模式下保存即生效；复制模式下每次改动后重新执行 sync 脚本。

## 常见问题
- 看不到父主题源码：父主题在站点文件系统 `wp-content/themes/astra/` 中，仓库不跟踪。直接在该路径打开供参考即可。
- 链接失败：脚本会尝试使用目录联接（`mklink /J`），若仍失败，请改用 `-Mode copy`。
- 线上部署：在 CI 或部署脚本中执行复制/同步到服务器 `wp-content`，或在服务器上拉取仓库并运行同步脚本（需按服务器环境调整）。

## 远程部署（阿里云 + 宝塔面板）

站点根路径（示例）：
- 标准：`/www/wwwroot/<你的站点目录或域名>`（目录下应有 `wp-config.php` 与 `wp-content/`）
- 在宝塔面板查看：网站 → 你的站点 → 网站目录；或 SSH/终端执行 `ls /www/wwwroot` 查找。

部署方式 A：手动上传（稳定、可控）
- 本地将以下目录分别打包为 zip：
  - `src/wp-content/themes/musicalbum-child`
  - `src/wp-content/plugins/musicalbum-integrations`
- 登录宝塔 → 文件 → 进入 `/www/wwwroot/<站点>/wp-content/themes/` 与 `/www/wwwroot/<站点>/wp-content/plugins/`，上传并解压。
- 在 WordPress 后台启用“Musicalbum Child”主题与“Musicalbum Integrations”插件。

部署方式 B：GitHub Actions SFTP 自动部署（高效、适合协作）
- 仓库已包含 `.github/workflows/deploy.yml` 工作流。
- 在仓库 Settings → Secrets and variables → Actions 配置：
  - `SFTP_HOST`：服务器 IP 或域名
  - `SFTP_PORT`：默认 `22`
  - `SFTP_USER` / `SFTP_PASSWORD`：SFTP 用户与密码（可用系统用户或宝塔创建的 FTP 账户，仅当其支持 SFTP）
  - `REMOTE_THEME_PATH`：如 `/www/wwwroot/<站点>/wp-content/themes/musicalbum-child`
  - `REMOTE_PLUGIN_PATH`：如 `/www/wwwroot/<站点>/wp-content/plugins/musicalbum-integrations`
- 推送到 `main` 分支时，将自动同步子主题与插件到对应远程路径。

提示：
- 同步不影响 `wp-content/uploads` 与数据库；数据迁移请用备份/迁移插件或数据库导入导出。
- 若服务器启用缓存（Redis/LiteSpeed/CDN），部署后需清缓存。