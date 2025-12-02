# Musicalbum WordPress 项目

仓库仅跟踪“自研代码”（子主题与集成插件）；第三方主题/插件与上传内容不纳入版本控制，保证干净的历史与可维护性。

## 仓库结构
- `src/wp-content/themes/musicalbum-child/`：子主题；用于在父主题 Astra 上进行模板与样式定制。
- `src/wp-content/plugins/musicalbum-integrations/`：集成插件；集中承载与第三方插件交互的 hooks/短码/接口。
- `scripts/sync.ps1`：开发同步脚本（复制/链接 `src` 到站点 `wp-content`）。
- `scripts/config.json`：同步脚本站点根配置（包含 `wp-content` 的站点目录）。
- `dev-stubs/`：本地 IDE 辅助桩（非运行时），提升自动补全与类型提示。
- `.github/workflows/main.yml`：自动部署工作流（FTP 部署子主题与插件）。
- `composer.json`：开发期 PHP/WordPress stubs 依赖。
- 运行站点的 `wp-content/` 存在于环境中，但不跟踪进 Git（由 `.gitignore` 忽略）。

提示：父主题源码不在仓库内；在本地站点 `wp-content/themes/astra/` 查看与参考，无需纳入 Git。

## 本地开发流程
- 安装父主题 Astra（后台 → 外观 → 主题）。
- 编辑 `scripts/config.json`，将 `sitePath` 设置为你的本地站点根目录，例如：
  - `C:\xampp\htdocs\your-site`（Windows 本地）
  - `/var/www/html/your-site`（Linux/WSL）
- 同步 `src` 到站点 `wp-content`：
  - 复制模式（稳定、免权限）：
    - `powershell -ExecutionPolicy Bypass -File .\scripts\sync.ps1 -ConfigPath .\scripts\config.json -Mode copy`
  - 链接模式（保存即生效，需管理员/开发者模式）：
    - `powershell -ExecutionPolicy Bypass -File .\scripts\sync.ps1 -ConfigPath .\scripts\config.json -Mode link`
  - 预演：`-DryRun` 仅打印将执行的操作。
- 在站点后台启用：主题 `Musicalbum Child` 与插件 `Musicalbum Integrations`。
- 开发位置：
  - 子主题样式与模板：`src/wp-content/themes/musicalbum-child/`（参考父主题同路径后覆写）。
  - 集成钩子与短码：`src/wp-content/plugins/musicalbum-integrations/`。

## 快速验证
- 新建页面并选择模板 `Musicalbum Verify`（模板文件：`src/wp-content/themes/musicalbum-child/page-templates/musicalbum-verify.php`）。
- 页面中包含短码输出，短码注册与实现位置：
  - 注册：`src/wp-content/plugins/musicalbum-integrations/musicalbum-integrations.php:19`（`register_shortcodes`）。
  - 实现：`src/wp-content/plugins/musicalbum-integrations/musicalbum-integrations.php:23`（返回 `Hello Musicalbum`）。

## 部署流程（CI）
- 推送到 `main` 自动触发 FTP 部署工作流（配置见 `.github/workflows/main.yml`）。
- 工作流要点：
  - 子主题部署步骤：`.github/workflows/main.yml:14-25`。
  - 插件部署步骤：`.github/workflows/main.yml:27-38`。
  - 需要在仓库 `Settings → Secrets and variables → Actions` 配置 `FTP_PASSWORD`。
- 部署仅同步子主题与插件目录；`uploads` 与数据库不受影响（数据迁移请使用备份/迁移工具）。

## 与第三方主题/插件的协作原则
- 不直接修改第三方主题/插件源码；在子主题通过模板覆写、样式覆盖与钩子进行扩展。
- 固化第三方版本并记录；升级先在测试环境验证再上线。
- 如需更严格的版本管理，可考虑 Composer/Bedrock（使用 `wpackagist.org` 获取依赖），但与当前结构不同，谨慎迁移。

## 注意事项 / 坑点
- 链接模式在 Windows 需管理员或开启开发者模式；失败请改用 `-Mode copy`。
- `scripts/config.json` 的 `sitePath` 必须指向站点根（包含 `wp-content`）；示例文件目前直接提供 `config.json`，按需修改即可。
- 父主题不在仓库；在 `wp-content/themes/astra/` 参考其模板后再复制到子主题路径进行覆写。
- 修改 CSS/JS 后确保正确入队：
  - 子主题样式入队：`src/wp-content/themes/musicalbum-child/functions.php:4-13`。
  - 插件资源入队：`src/wp-content/plugins/musicalbum-integrations/musicalbum-integrations.php:27-31`。
- 服务器侧若开启缓存（Redis/LiteSpeed/CDN），部署后需要清理缓存。
- 更换服务器或账号时更新工作流目标与凭据，避免部署失败。

## 常见问题
- 看不到父主题代码：父主题在运行环境 `wp-content/themes/astra/`，不在仓库；本地直接打开该路径参考即可。
- 如何在父主题基础上开发：复制父主题对应模板到子主题同路径后修改，并在 `functions.php` 编写钩子逻辑。
- 改动如何上线：推送到 `main` 分支后由 CI 自动部署到服务器（需预先配置 `FTP_PASSWORD`）。

——以上内容旨在让队员快速理解“该改哪、怎么跑、如何上线、有哪些坑”。如需补充具体业务流程，可在本 README 继续扩展。


