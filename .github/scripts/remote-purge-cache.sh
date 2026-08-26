#!/usr/bin/env bash
# Runs ON the Hostinger server (piped in via ssh). Best-effort cache purge:
# WordPress object/transient cache, LiteSpeed cache, and on-disk cache dirs.
#
# Usage (via ssh): bash -s -- <WP_DIR>
set -euo pipefail

RAW_WP_DIR="${1:?WP_DIR is required}"
if [[ "${RAW_WP_DIR}" == "~/"* ]]; then
  WP_DIR="${HOME}/${RAW_WP_DIR#~/}"
else
  WP_DIR="${RAW_WP_DIR}"
fi

echo "Purging caches for WP root: ${WP_DIR}"

if command -v wp >/dev/null 2>&1 && [ -f "${WP_DIR}/wp-load.php" ]; then
  echo "wp-cli detected — flushing object cache, transients, LiteSpeed ..."
  wp --path="${WP_DIR}" cache flush 2>/dev/null || true
  wp --path="${WP_DIR}" transient delete --all 2>/dev/null || true
  # LiteSpeed Cache plugin (installed on this site).
  wp --path="${WP_DIR}" litespeed-purge all 2>/dev/null || true
  wp --path="${WP_DIR}" litespeed-purge all --all 2>/dev/null || true
else
  echo "wp-cli not available — falling back to on-disk purge only."
fi

# On-disk caches (safe to remove; regenerated on next request).
for d in \
  "${WP_DIR}/wp-content/cache" \
  "${WP_DIR}/wp-content/litespeed"; do
  if [ -d "${d}" ]; then
    rm -rf "${d:?}/"* 2>/dev/null || true
    echo "cleared ${d}"
  fi
done

# Drop a stale persistent object-cache dropin's data if present (LiteSpeed/Redis
# handles its own flush above; we don't delete the dropin file itself).
echo "Cache purge complete."
