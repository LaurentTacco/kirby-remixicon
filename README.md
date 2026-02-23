# Kirby Remix Icon

Use **3000+ [Remix Icon](https://remixicon.com)** icons directly in your Kirby Panel blueprints — pages, sections, tabs, and more.

No icons are shipped with this plugin. They are automatically downloaded from the [official Remix Icon repository](https://github.com/Remix-Design/RemixIcon) on first Panel load.

![Kirby 4+](https://img.shields.io/badge/Kirby-4%2B-black)
![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-777BB4)
![License MIT](https://img.shields.io/badge/License-MIT-green)

---

## Installation

### Composer (recommended)

```bash
composer require laurenttacco/kirby-remixicon
```

That's it. The icons are downloaded automatically the first time you open the Kirby Panel.

### Manual (Git)

```bash
cd site/plugins
git clone https://github.com/LaurentTacco/kirby-remixicon.git
php site/plugins/kirby-remixicon/update.php
```

### Manual (Download)

1. Download and extract the [latest release](https://github.com/LaurentTacco/kirby-remixicon/releases)
2. Copy the `kirby-remixicon` folder into `site/plugins/`
3. Run: `php site/plugins/kirby-remixicon/update.php`

---

## Usage

Use any [Remix Icon](https://remixicon.com) name in your blueprints. Each icon comes in two styles:

| Style | Suffix | Example |
|-------|--------|---------|
| Outlined | `-line` | `home-line` |
| Solid | `-fill` | `home-fill` |

### Page icon

```yaml
# site/blueprints/pages/project.yml
title: Project
icon: folder-3-line
```

### Section icons

```yaml
# site/blueprints/pages/default.yml
sections:
  gallery:
    type: pages
    label: Gallery
    icon: gallery-view-2

  articles:
    type: pages
    label: Articles
    icon: article-line

  files:
    type: files
    label: Documents
    icon: attachment-line
```

### Tab icons

```yaml
tabs:
  content:
    icon: file-text-line
    label: Content
  seo:
    icon: search-line
    label: SEO
  settings:
    icon: settings-3-line
    label: Settings
```

Browse all available icons at **[remixicon.com](https://remixicon.com)**.

---

## Updating icons

Run the update script to fetch the latest icons from GitHub:

```bash
php site/plugins/kirby-remixicon/update.php
```

The script compares versions and skips the download if already up to date.

### Automate with Composer

Add this to your project's `composer.json` to update icons on every `composer update`:

```json
{
  "scripts": {
    "post-update-cmd": [
      "@php site/plugins/kirby-remixicon/update.php"
    ]
  }
}
```

---

## Version info

The current Remix Icon version is visible in the Kirby Panel under **System > kirby-remixicon**.

---

## Requirements

- **Kirby** 4 or 5
- **PHP** 8.1+ with `zip` extension (enabled by default on most hosts)

---

## How it works

1. On first Panel load, `index.php` detects that no icons are present (`index.js` is empty)
2. It downloads the [Remix Icon repository](https://github.com/Remix-Design/RemixIcon) as a zip archive
3. All SVG paths are extracted and compiled into a single `index.js` file
4. Kirby's Panel loads this file and makes every icon available in blueprints

No Node.js, npm, or build step required.

---

## License

- **Plugin**: [MIT](LICENSE)
- **Remix Icon**: [Apache 2.0](https://github.com/Remix-Design/remixicon/blob/master/License) by [Remix Design](https://remixicon.com)
