#!/usr/bin/env bash
#
# Build an installable WordPress theme package (acreline.zip).
#
# Sage ships compiled assets (public/build) and a production Composer
# autoloader so the target host needs no composer/npm.
#
# Usage:
#   bin/build-theme-zip.sh            # -> dist-theme/acreline.zip
#   OUT=/tmp/pkg bin/build-theme-zip.sh
#
set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SLUG="acreline"
OUT_DIR="${OUT:-$THEME_DIR/dist-theme}"
STAGE_PARENT="$(mktemp -d)"
STAGE="$STAGE_PARENT/$SLUG"

echo "==> Building theme assets (npm run build)"
( cd "$THEME_DIR" && npm run build )

if [ ! -f "$THEME_DIR/public/build/manifest.json" ]; then
  echo "ERROR: public/build/manifest.json missing — asset build failed." >&2
  exit 1
fi

echo "==> Staging runtime files"
mkdir -p "$STAGE"
for item in app resources public functions.php index.php style.css screenshot.png \
            comments.php theme.json composer.json composer.lock LICENSE LICENSE.md \
            license.txt readme.txt CREDITS.md CHANGELOG.md languages; do
  if [ -e "$THEME_DIR/$item" ]; then
    cp -a "$THEME_DIR/$item" "$STAGE/$item"
  fi
done

echo "==> Installing production Composer dependencies (--no-dev)"
( cd "$STAGE" && composer install --no-dev --optimize-autoloader --no-interaction --no-progress )

echo "==> Creating zip"
mkdir -p "$OUT_DIR"
ZIP="$OUT_DIR/$SLUG.zip"
rm -f "$ZIP"
( cd "$STAGE_PARENT" && zip -rqX "$ZIP" "$SLUG" -x '*.DS_Store' )
rm -rf "$STAGE_PARENT"

echo "==> Created $ZIP ($(du -h "$ZIP" | cut -f1))"
echo "    Install with: wp theme install \"$ZIP\" --activate"
echo "    or upload via WP Admin -> Appearance -> Themes -> Add New -> Upload Theme"
