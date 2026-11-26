# 自动部署到 IONOS

设置好以后，**合并到 `main`，几分钟内正式网站的主题就会自动更新**，不用再手动上传主题包。

- `main` → 正式网站（GitHub 环境 `production`）
- `staging` → 测试网站（GitHub 环境 `staging`，可选）

## 会改什么，不会改什么

部署**只替换**服务器上的这一个文件夹：

```
wp-content/themes/wildlife-interactions/
```

| 不会被碰到 | |
|---|---|
| 数据库 | 客户写的所有文字、任务、人才、页面、设置 |
| `wp-content/uploads/` | 客户上传的所有图片和文件 |
| `wp-content/plugins/` | 插件 |
| `wp-config.php` | WordPress 配置 |

为了安全，如果填写的路径不是以 `/wp-content/themes/wildlife-interactions` 结尾，部署会直接停止，不会上传任何文件。

---

## 第一次设置（只需做一次）

### 前提
正式网站上已经**手动上传过一次主题**并启用（外观 › 主题），并且运行过导入。自动部署只负责**更新**主题。

### 1. 在 IONOS 找到 SFTP 信息

登录 IONOS → **Hébergement / Hosting** → 选择你的套餐 → **SFTP & SSH**。如果是 Managed WordPress，就在 WordPress 网站的管理页面里找 **SFTP**。

需要记下这几项：

| 信息 | 例子 |
|---|---|
| 主机名（Serveur / Host） | `access-1234567.webspace-host.com` |
| 用户名 | `a123456` 或 `u12345678-xxxx` |
| 密码 | 如果不记得，可以在同一页面重置，或新建一个 SFTP 用户 |

**主题文件夹路径**：用 [Cyberduck](https://cyberduck.io) 或 FileZilla 以 SFTP 方式登录，找到 `wp-content/themes/wildlife-interactions` 文件夹，复制它的**完整路径**。例如：

```
/clickandbuilds/WordPress/MonSite/wp-content/themes/wildlife-interactions
```

### 2. 把信息填进 GitHub（加密保存，任何人都看不到）

1. 打开仓库 → **Settings** → 左侧 **Environments** → **New environment**
2. 名称填 **`production`** → **Configure environment**
3. （建议）勾选 **Required reviewers** 并选你自己。这样每次部署到正式网站前，GitHub 会先等你点“批准”
4. 在 **Environment secrets** 里点 **Add environment secret**，依次添加：

| Name | Value |
|---|---|
| `SFTP_HOST` | 主机名 |
| `SFTP_USER` | 用户名 |
| `SFTP_PASSWORD` | 密码 |
| `SFTP_THEME_DIR` | 主题文件夹的完整路径（第 1 步复制的） |

如果有测试网站，再建一个 **`staging`** 环境，填入测试网站的 4 项信息。

### 3. 试运行
仓库 → **Actions** → **Deploy WordPress theme** → **Run workflow** → 选择环境 → **Run workflow**。
绿色 ✓ 表示成功，然后打开网站检查。

---

## 以后的日常流程

1. 让 Claude 在 `dev` 分支上改设计，在 Local 上确认
2. 合并到 `staging`：测试网站自动更新，可以给客户预览
3. 合并到 `main`：正式网站自动更新（如果开了 Required reviewers，需要先在 Actions 里点批准）
4. 如果改动没出现，清除一下 IONOS 的缓存

**每次部署都会生成新的版本号**，访客浏览器会自动加载新的样式和脚本，不会停留在旧缓存上。

只有改了网站代码（`css/`、`js/`、`assets/`、`wordpress/`）才会触发部署；只改说明文档不会部署。

## 出问题时
- **Actions 里显示红色 ✗**：点进去看是哪一步失败。常见原因是密码错误或路径不对
- **显示黄色警告 “No SFTP settings”**：这个环境还没填 secrets，所以没有部署任何文件（不算错误）
- **想回到上一个版本**：在 GitHub 上 revert 那次合并，或者重新运行上一次成功的部署（Actions → 选那次运行 → Re-run jobs）
