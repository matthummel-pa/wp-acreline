---
name: wordpress-studio-setup
description: Bootstrap a local WordPress site for Acreline theme edits. Mac uses WordPress Studio; Linux/Cloud uses bin/setup-wp.sh. Never delete Studio sites unless the user explicitly asks.
---

# wordpress-studio-setup (Acreline)

Use this skill when Matt asks to set up a **local WordPress site** for this theme (`acreline`), sync Cursor with Studio, or spin up a `.local` site.

Tracker: Notion **Dev To-Do — Future WordPress Projects** (project slug `acreline`). Human SOP: *SOP — Local WordPress with Studio & Cursor*.

This repo is the **Sage 11 theme only**. WordPress core lives outside git.

## Hard rules

- Never run `studio site delete` or `studio preview delete` unless Matt explicitly asks to destroy that site.
- Never commit `wp-config.php`, `.env`, or database dumps with passwords.
- Never push a local database to production.
- Verify on the **rendered local URL**, not only the Site Editor.
- Playground and Studio do **not** share a database.
- Do not copy a Compass/`theme/` kit into this project. Activate **acreline**.

## Detect the machine

1. If `uname` is Darwin and `studio --help` works (or can be installed): **Mac / Studio path**.
2. Otherwise: **non-Mac path**. Do **not** run `studio site create`.

## Non-Mac / Cloud Agent (this VM)

```bash
bin/setup-wp.sh
wp server --path="$HOME/wp" --host=0.0.0.0 --port=8080 --allow-root
```

- Site: `http://localhost:8080/`
- Admin: `/wp-admin` — user `admin`, password `admin123`
- Theme path: `$HOME/wp/wp-content/themes/acreline` → symlink to this repo
- After Blade edits: `wp acorn view:clear --path="$HOME/wp" --allow-root`

Done when homepage, `/listings`, and `/wp-admin` load without a Vite-manifest error.

## Mac / Studio

Skip `scripts/mac-setup.sh` from the Compass kit if `studio --help` already works.

Slug default: `acreline`.

```bash
mkdir -p ~/Studio/acreline
studio site create --path ~/Studio/acreline --https --domain acreline.local
# Symlink or copy THIS repo (not Compass) to:
#   ~/Studio/acreline/wp-content/themes/acreline
studio wp theme activate acreline --path ~/Studio/acreline
studio site start --path ~/Studio/acreline
cd /path/to/wp-acreline && composer install && npm install && npm run build
```

Prefer a **symlink** from `~/Studio/acreline/wp-content/themes/acreline` to the git clone so local edits stay in git.

Cursor workspace for a running site: WordPress root `~/Studio/acreline` **or** the theme clone if that is where git lives.

MCP (`.cursor/mcp.json` in this repo):

```json
{
  "mcpServers": {
    "wordpress-studio": {
      "command": "studio",
      "args": ["mcp"]
    }
  }
}
```

If MCP is red, launch Cursor from Terminal after Homebrew: `eval "$(/opt/homebrew/bin/brew shellenv)"` then `cursor .`

Done when `studio site status` shows a URL that loads, **acreline** is the active theme, and you have not deleted any sites.

## Daily prompts after setup

- Start this Studio site and give me the URL.
- List plugins via Studio WP-CLI (Mac) or `wp plugin list --path="$HOME/wp"`.
- Do not delete sites.
