#!/bin/bash
# Builds the WordPress theme: wordpress/theme (PHP) + the site's css/, js/, assets/
# → dist/wildlife-interactions/ and dist/wildlife-interactions.zip (to upload in
#   WordPress: Apparence › Thèmes › Ajouter › Téléverser un thème).
#
#   ./wordpress/build-theme.sh                      build the zip
#   ./wordpress/build-theme.sh ~/Local\ Sites/wi/app/public/wp-content/themes
#                                                   also copy the theme into that folder (Local)
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/dist/wildlife-interactions"

rm -rf "$OUT" "$ROOT/dist/wildlife-interactions.zip"
mkdir -p "$OUT"
rsync -a --exclude '.DS_Store' "$ROOT/wordpress/theme/" "$OUT/"
rsync -a --exclude '.DS_Store' "$ROOT/css" "$ROOT/js" "$ROOT/assets" "$OUT/"
# Content data files are replaced by WordPress content (inc/assets.php)
rm -f "$OUT/js/zones.js" "$OUT/js/talents-data.js" "$OUT/js/detail-content.js"

(cd "$ROOT/dist" && zip -qr wildlife-interactions.zip wildlife-interactions)
echo "✓ dist/wildlife-interactions.zip ($(du -h "$ROOT/dist/wildlife-interactions.zip" | cut -f1))"

if [ $# -ge 1 ]; then
  rsync -a --delete "$OUT/" "$1/wildlife-interactions/"
  echo "✓ copied to $1/wildlife-interactions"
fi
