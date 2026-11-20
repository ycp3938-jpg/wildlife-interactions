# Wildlife Interactions：WordPress 主题

把这个静态网站改成了一个 WordPress 主题，**客户可以在后台修改所有文字和图片**，设计、动画和 3D 地球仪保持不变。

目前是**法语单语言**网站。以后如果需要英文版，主题已经兼容 Polylang 插件，见文末“以后加英文版”。

## 文件结构

| 位置 | 内容 |
|---|---|
| `wordpress/theme/` | 主题的 PHP 部分：模板、后台字段、内容类型、联系表单、导入工具 |
| `css/`、`js/`、`assets/`（仓库根目录） | 和静态网站**共用**的样式、脚本、字体、Logo |
| `wordpress/theme/seed/` | 现有网站的内容（文字和图片路径），只在第一次导入时使用 |
| `wordpress/build-theme.sh` | 把上面几部分打包成 `dist/wildlife-interactions.zip` |

改样式或脚本时，直接改根目录的 `css/`、`js/`，静态网站和 WordPress 主题会同时生效。

## 打包主题

```bash
./wordpress/build-theme.sh
```

生成 `dist/wildlife-interactions.zip`（约 10 MB，`dist/` 不提交到 Git）。

## 安装步骤（本地 Local，或 IONOS）

1. **安装插件**（后台 › 插件 › 安装插件）：
   - **Secure Custom Fields**（必需，WordPress 官方免费插件，用来提供后台编辑表单）
   - 建议在 IONOS 上再装 **WP Mail SMTP**，让联系表单的邮件从 IONOS 邮箱发出，避免进垃圾邮件
2. **上传主题**：外观 › 主题 › 添加 › 上传主题 → 选择 `wildlife-interactions.zip` → 启用
   - IONOS 默认上传限制可能小于 10 MB。如果上传失败，用 SFTP 把 `dist/wildlife-interactions/` 文件夹上传到 `wp-content/themes/`
3. **导入内容**：工具 › **Importer le site WI** → 点 **Importer le contenu**
   - 会自动创建 6 个页面、11 个任务和教育项目、19 位人才，并把约 70 张图片导入媒体库，同时设置好首页
4. 打开网站检查

## 客户在后台修改的地方

| 后台菜单 | 内容 |
|---|---|
| **Missions & Éducation** | 每个任务和教育项目：列表文字、详情页（概览、4 个挑战、数字、段落、团队）、地球仪上的位置 |
| **Talents** | 人物：照片（特色图片）、职位、简介、LinkedIn。顺序按“属性 › 顺序”排列 |
| **页面 › Accueil** | 首页文字、合作伙伴 Logo、数字、常见问题，以及 **Réglages du site**（页脚、邮箱、LinkedIn、“Nous soutenir”按钮） |
| **页面 › Talents / Exposition & média / Contact** | 每页的标题、文字、图片 |
| **外观 › 菜单**（可选） | 默认自动生成菜单。如果想自己调整，就建一个菜单，放到“Menu principal”位置 |

## 以后加英文版

主题已经为双语做好准备，不需要改代码：
1. 安装并启用 **Polylang**，在“语言”里添加 **Français**（默认）和 **English**
2. 进入“语言”页面，按提示把已有内容都设为法语
3. 用 Polylang 为每个页面、任务和人才创建英文翻译；菜单旁会自动出现 FR / EN 切换
4. 按钮等固定文字（“Aperçu”“Enjeux”、表单字段等）在“语言 › 翻译”里翻译

如果是全新站点，也可以在第 3 步“导入内容”时勾选“创建英文版本”，自动生成英文副本（内容是法语，需要再翻译）。

## 开发说明

- 页面脚本读取的是 `window.WI_*` 数据（`inc/assets.php` 根据 WordPress 内容生成），和静态网站 `js/zones.js` 等文件里的数据格式相同，所以同一套 JS 两边都能用
- 后台字段定义在 `inc/fields.php`（用代码注册，跟着主题走，不需要在后台手动建字段）
- 导入可以重复执行：勾选“Supprimer et réimporter”会删除上次导入的内容后重新导入，图片会复用，不会重复上传
