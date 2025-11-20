# Musicalbum WordPress 项目

本仓库仅跟踪“自研代码”（子主题与集成插件），第三方主题与插件不直接纳入版本控制。这样可以安全地在第三方主题/插件基础上开发，同时避免把不可维护的内容混入历史。

## 目录结构
- `src/wp-content/themes/musicalbum-child/`：子主题（用于在父主题基础上定制）
- `src/wp-content/plugins/musicalbum-integrations/`：集成插件（用于通过钩子与第三方插件协作）
- 站点根下的 `wp-content/` 会在运行环境中存在，但通常不进仓库（由 `.gitignore` 忽略）。

## 父主题获取与安装
你需要在运行的 WordPress 环境中安装父主题，子主题才会工作：
1. 在后台：`外观 → 主题 → 添加`，搜索或上传父主题 ZIP 安装并启用（或先启用父主题后再切换到子主题）。
2. 安装后，父主题代码会出现在站点文件系统的 `wp-content/themes/<父主题目录名>/` 下（本仓库默认不跟踪该目录）。
3. 在子主题 `style.css` 中，将 `Template:` 字段改为父主题的目录名（例如 `Template: hello-elementor`）。

> 如果你需要在 IDE 中“查看父主题源码”，可直接在本地站点的 `wp-content/themes/<父主题目录名>/` 打开文件进行参考；无需把它加入 Git。

## 子主题激活与同步
本仓库中的子主题代码位于 `src/`，建议以下同步流程：
- 将 `src/wp-content/themes/musicalbum-child/` 复制到站点的 `wp-content/themes/` 下。
- 在后台启用 `Musicalbum Child` 主题。
- 之后的开发都在仓库的 `src` 目录进行，保存后定期复制到站点 `wp-content` 进行验证（或在 CI/部署脚本中自动同步）。

## 集成插件激活与同步
- 将 `src/wp-content/plugins/musicalbum-integrations/` 复制到站点的 `wp-content/plugins/` 下。
- 在后台插件页启用 `Musicalbum Integrations`。
- 将所有与第三方插件的钩子（actions/filters）、短码、REST 接口等，集中写在该插件内，避免直接改第三方插件源码。

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
- “如何把改动上线”：将 `src` 下的子主题与插件复制/同步到服务器的 `wp-content`；或在 CI 脚本中做自动化同步。

## 后续需要你提供的信息
- 父主题的目录名（用于设置 `style.css` 的 `Template:` 字段）。
- 将使用的第三方插件列表（我可以补充具体的 hooks 接入示例）。
