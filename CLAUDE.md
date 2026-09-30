@AGENTS.md

# Claude notes

Cursor and Claude share one set of rules. `AGENTS.md` (above) and `.cursor/rules/*.mdc` are the source of truth — edit those, not this file, when an agent repeats a mistake.

## Always-on rules

@.cursor/rules/keystone-project.mdc
@.cursor/rules/wp-review.mdc

## Read before editing matching files

| Rule | Applies to | What it covers |
| --- | --- | --- |
| `.cursor/rules/accessibility.mdc` | `resources/css/**/*.css,resources/js/**/*.js,resources/views/**/*.blade.php` | WCAG 2.2 AA — contrast, color-independent status, focus, 44px targets, semantics, reduced motion |
| `.cursor/rules/conversion-ux.mdc` | `resources/css/**/*.css,resources/js/**/*.js,resources/views/**/*.blade.php` | High-conversion realtor UX — visible labels, input types, button states, aria-live |
| `.cursor/rules/frontend.mdc` | `resources/css/**/*.css,resources/js/**/*.js,resources/views/**/*.blade.php` | Keystone CSS tokens, homepage search, and front-end JS |
| `.cursor/rules/live-wordpress.mdc` | `on request` | Seeding or editing the live Acreline WordPress site (WPVibe, WP-CLI emulator, host cache) |
| `.cursor/rules/marketplace.mdc` | `app/**/*.php,resources/views/**/*.blade.php,bin/**/*.sh,plugins/**/*.php,docs/marketplace/**` | ThemeForest / WordPress.org / own-site seller rules — plugin territory, WPCS, demo assets, footer credits |
| `.cursor/rules/seo-native.mdc` | `app/setup.php,app/Support/**/*.php,resources/views/layouts/**/*.blade.php,resources/views/**/*.blade.php` | Native SEO — title, description, canonical, Open Graph, Twitter cards. No SEO plugins. |
| `.cursor/rules/theme-sage.mdc` | `app/**/*.php,resources/views/**/*.blade.php` | Sage 11 theme, CPTs, blocks, and Blade conventions |
| `.cursor/rules/theme-shop-readme.mdc` | `README.md,readme.txt,SUPPORT.md,.gitignore,style.css,docs/marketplace/**,bin/build-*.sh,composer.json,package.json` | Prep README / readme.txt / SUPPORT for ThemeForest, TemplateMonster, and own-site theme shops — Sage 11 zip vs git, gitignore, listing copy |
| `.cursor/rules/ui-aesthetics.mdc` | `resources/css/**/*.css,resources/views/**/*.blade.php` | Modern UI aesthetics — fluid layout, cards, type scale, and photo-overlay contrast |

## Workflow

- `/plan <idea>` — think it through before coding.
- `/review [PR#]` — senior WordPress review (uses the `wp-reviewer` subagent).
- `/ship` — checks, commit, push, PR.
- `python3 .github/scripts/wp-review` — WordPress Handbook checks on changed lines. Checklist: `.github/review-checklist.md`.
- Local WordPress: WordPress Studio (`studio wp …`, never plain `wp` against a Studio site). Clear Blade cache with `studio wp acorn view:clear` after template edits.
- Setting up a local site: follow `.cursor/skills/wordpress-studio-setup/SKILL.md` (Studio on Mac, `bin/setup-wp.sh` on Linux/Cloud). Never run `studio site delete`.
