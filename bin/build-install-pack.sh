#!/usr/bin/env bash
#
# General install pack for a regular WordPress host (Appearance → Upload Theme).
#
# Buyers extract this zip, then upload the inner acreline.zip.
# Also ships child theme, Acreline Core, and seller/buyer requirements docs.
# Does not include the internal seller brief (SELLING.md).
#
# Usage:
#   bin/build-install-pack.sh
#   OUT=/tmp/acreline-pack bin/build-install-pack.sh
#
set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="${OUT:-$THEME_DIR/dist-install}"
STAGE="$(mktemp -d)"
PACK_ROOT="$STAGE/acreline-pack"

cleanup() { rm -rf "$STAGE"; }
trap cleanup EXIT

version_from_style() {
  sed -n 's/^[[:space:]]*Version:[[:space:]]*//p' "$THEME_DIR/style.css" | head -n1 | tr -d '[:space:]'
}

VERSION="$(version_from_style)"
if [ -z "$VERSION" ]; then
  echo "ERROR: could not read Version from style.css" >&2
  exit 1
fi

THEME_ZIP="$THEME_DIR/dist-theme/acreline.zip"
if [ "${SKIP_THEME_BUILD:-}" = "1" ] && [ -f "$THEME_ZIP" ]; then
  echo "==> Reusing existing theme zip ($THEME_ZIP)"
else
  echo "==> Theme zip (regular WP upload)"
  ( cd "$THEME_DIR" && bin/build-theme-zip.sh )
fi
if [ ! -f "$THEME_ZIP" ]; then
  echo "ERROR: $THEME_ZIP missing" >&2
  exit 1
fi

echo "==> Child theme zip"
mkdir -p "$STAGE/acreline-child"
cp -a "$THEME_DIR/child-theme/." "$STAGE/acreline-child/"
( cd "$STAGE" && zip -rqX acreline-child.zip acreline-child -x '*.DS_Store' )

echo "==> Acreline Core zip"
mkdir -p "$STAGE/acreline-core"
cp -a "$THEME_DIR/plugins/acreline-core/." "$STAGE/acreline-core/"
( cd "$STAGE" && zip -rqX acreline-core.zip acreline-core -x '*.DS_Store' )

echo "==> Assembling acreline-pack/"
mkdir -p "$PACK_ROOT/Documentation/assets" "$PACK_ROOT/Documentation/screenshots" "$PACK_ROOT/Demos" "$PACK_ROOT/Licensing"
cp -a "$THEME_DIR/docs/marketplace/PACK-README.txt" "$PACK_ROOT/README.txt"
cp -a "$THEME_ZIP" "$PACK_ROOT/acreline.zip"
cp -a "$STAGE/acreline-child.zip" "$PACK_ROOT/"
cp -a "$STAGE/acreline-core.zip" "$PACK_ROOT/"
# Buyer HTML hub. Seller-only markdown (SELLING.md, themeforest-listing.md,
# wordpress-org.md) stays out of this zip.
DOCS="$THEME_DIR/docs/marketplace"
for html in index.html buyer-guide.html branding.html requirements.html support.html \
           changelog.html sources.html customizer.html templates.html listings.html \
           child-theme.html translation.html faq.html credits.html; do
  if [ -f "$DOCS/$html" ]; then
    cp -a "$DOCS/$html" "$PACK_ROOT/Documentation/"
  fi
done
cp -a "$DOCS/assets/." "$PACK_ROOT/Documentation/assets/"
if [ -d "$DOCS/screenshots" ]; then
  cp -a "$DOCS/screenshots/." "$PACK_ROOT/Documentation/screenshots/"
fi
cp -a "$THEME_DIR/SUPPORT.md" "$PACK_ROOT/Documentation/"
cp -a "$THEME_DIR/readme.txt" "$PACK_ROOT/Documentation/"
cp -a "$THEME_DIR/docs/marketplace/Demos/README.txt" "$PACK_ROOT/Demos/"
cp -a "$THEME_DIR/CREDITS.md" "$PACK_ROOT/Licensing/"
cp -a "$THEME_DIR/LICENSE.md" "$PACK_ROOT/Licensing/" 2>/dev/null || true
cp -a "$THEME_DIR/license.txt" "$PACK_ROOT/Licensing/" 2>/dev/null || true

if [ -d "$HOME/wp" ] && command -v wp >/dev/null 2>&1; then
  echo "==> Exporting WXR from local WP (if available)"
  wp export --path="$HOME/wp" --dir="$PACK_ROOT/Demos" --allow-root 2>/dev/null || true
fi

echo "==> Writing pack zip"
rm -rf "$OUT_DIR"
mkdir -p "$OUT_DIR"
ZIP_VERSIONED="$OUT_DIR/acreline-$VERSION.zip"
rm -f "$ZIP_VERSIONED"
( cd "$STAGE" && zip -rqX "$ZIP_VERSIONED" acreline-pack -x '*.DS_Store' )

# Unzipped tree for inspection (same files as inside the zip)
cp -a "$PACK_ROOT" "$OUT_DIR/acreline-pack"

echo "==> Pack ready"
echo "    Regular WP upload:  acreline-pack/acreline.zip  (inside the pack)"
echo "    Seller upload:      $ZIP_VERSIONED"
du -h "$ZIP_VERSIONED" "$PACK_ROOT/acreline.zip" | sed 's/^/    /'
find "$OUT_DIR/acreline-pack" -type f | sort
