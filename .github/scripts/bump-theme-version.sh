#!/usr/bin/env bash
# Bump the "Version:" header in each theme's style.css so browsers re-fetch
# CSS/JS that are enqueued with the theme version as ?ver=.
#
# This runs on the CI runner only and is NOT committed back to the repo —
# it just changes the copy of the files that gets rsync'd to the server.
#
# Usage: bump-theme-version.sh <build-id>
#   THEME_SRC_BASE  e.g. "." (theme dirs live at the repo root here)
#   THEME_SLUGS     space separated, e.g. "orto orto-child"
set -euo pipefail

BUILD_ID="${1:?usage: bump-theme-version.sh <build-id>}"
BASE="${THEME_SRC_BASE:-.}"
SLUGS="${THEME_SLUGS:-orto orto-child}"

for slug in ${SLUGS}; do
  CSS="${BASE}/${slug}/style.css"
  if [ ! -f "${CSS}" ]; then
    echo "skip: ${CSS} not found"
    continue
  fi

  # Read existing version (first "Version:" line in the header block).
  CURRENT="$(grep -iE '^[[:space:]*]*Version:' "${CSS}" | head -n1 | sed -E 's/.*Version:[[:space:]]*//I; s/[[:space:]]*$//')"
  if [ -z "${CURRENT}" ]; then
    echo "skip: no Version header in ${CSS}"
    continue
  fi

  # Strip any previous build suffix we appended, then append the new one.
  CLEAN_BASE_VER="${CURRENT%%+build*}"
  NEW="${CLEAN_BASE_VER}+build.${BUILD_ID}"

  # Replace only the version value on the matching header line (case-insensitive).
  perl -0pi -e "s/(Version:[ \t]*).*/\${1}${NEW}/i" "${CSS}"
  echo "bumped ${slug}: ${CURRENT} -> ${NEW}"
done
