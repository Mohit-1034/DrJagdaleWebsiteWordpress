# Dr. Jagdale Website

WordPress theme source for the Dr. Jagdale clinic website, built on the
[Orto](https://orto.ancorathemes.com/) premium theme (AncoraThemes) with a child
theme for all site-specific customization.

## Contents

| Path | Description |
| --- | --- |
| `orto/` | Parent theme (vendor code — do not edit) |
| `orto-child/` | Child theme — all custom styles and functions live here |

The parent theme ships bundled integrations for Elementor, WooCommerce,
The Events Calendar, GiveWP, MetForm, Tutor LMS, WPML and TRX Addons
(`orto/plugins/`), plus a skin system under `orto/skins/`.

## Requirements

- WordPress 5.5+ (tested up to 6.8)
- PHP 7.4+
- Orto theme licence from AncoraThemes

## Installation

Clone into the WordPress themes directory:

```bash
cd wp-content/themes
git clone <repo-url> DrJagdaleWebSite
mv DrJagdaleWebSite/orto DrJagdaleWebSite/orto-child .
rmdir DrJagdaleWebSite
```

Then in **Appearance → Themes**, activate **Orto Child Theme**. Install the
recommended plugins when WordPress prompts, and import the demo content if
starting from a clean site.

## Development

All changes go in `orto-child/`. Editing `orto/` breaks the upgrade path — a
theme update from AncoraThemes overwrites it.

- **Styles** — `orto-child/style.css`, responsive overrides in `orto-child/responsive.css`
- **PHP** — `orto-child/functions.php` (hooks, filters, template overrides)
- **Template overrides** — copy the file from `orto/` into `orto-child/`, keeping the same relative path

### Updating the parent theme

1. Download the new Orto release from AncoraThemes.
2. Replace the `orto/` directory wholesale.
3. Verify child-theme overrides still match the parent's markup, then commit.

See `orto/changelog.txt` for the parent theme's release history.

## Licence

Parent and child themes are licensed under the
[GNU GPL v2 or later](http://www.gnu.org/licenses/gpl-2.0.html).
Site-specific content and assets are property of Dr. Jagdale.
