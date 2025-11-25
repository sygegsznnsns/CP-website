# Musicalbum WordPress 项目

本仓库仅跟踪“自研代码”（子主题与集成插件），第三方主题与插件不直接纳入版本控制。这样可以安全地在第三方主题/插件基础上开发，同时避免把不可维护的内容混入历史。

## 目录结构
- `src/wp-content/themes/musicalbum-child/`：子主题（用于在父主题基础上定制）
- `src/wp-content/plugins/musicalbum-integrations/`：集成插件（用于通过钩子与第三方插件协作）
- 站点根下的 `wp-content/` 会在运行环境中存在，但通常不进仓库（由 `.gitignore` 忽略）。

> 如果你需要在 IDE 中“查看父主题源码”，可直接在本地站点的 `wp-content/themes/<父主题目录名>/` 打开文件进行参考；无需把它加入 Git。

## 与第三方主题/插件的协作原则
- 不直接修改第三方主题/插件源码；通过子主题覆写模板、样式和钩子实现定制。
- 固化第三方版本：在项目文档中记录版本；升级前先在测试环境验证。
- 如果需要严格锁定版本，可迁移到 Composer/Bedrock 管理（例如使用 `wpackagist.org` 获取主题/插件依赖）。

## 可选：以 Submodule/Composer 管理父主题代码（仅供参考）
- Submodule：若父主题开源且你希望只读引用其源码，可将其作为子模块引入：
  ```bash
  git submodule add <父主题仓库URL> external/themes/<父主题目录名>
  ```
  然后在 IDE 中参考该源码，但不要将其与站点运行路径混用。
- Composer（Bedrock 结构）：
  - 使用 `johnpbloch/wordpress` 管理 WP 核心，使用 `wpackagist-theme/<slug>` 管理主题。
  - 适合需要严控版本与自动部署的团队，但与当前经典结构略有差异。

## 常见问题
- “看不到父主题代码”：因为 `.gitignore` 忽略了第三方主题，父主题代码在你的运行环境 `wp-content/themes/` 中可见，但不在本仓库内。按上文安装后即可在文件系统中查看。
- “如何在父主题基础上开发”：在子主题中复制并覆写父主题对应模板文件路径（例如 `woocommerce/single-product.php`），并在 `functions.php` 编写钩子与自定义逻辑。
- “如何把改动上线”：陈攀已经写好了自动部署脚本，push到本仓库自动部署到服务器


