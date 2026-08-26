#!/usr/bin/env bash
# Add the Hostinger host key to a known_hosts file and export SSH_OPTS for
# subsequent workflow steps (so we use StrictHostKeyChecking=yes safely).
#
#   SSH_HOST         host (already cleaned of user@ / :port)
#   SSH_PORT         ssh port (default 65002)
#   SSH_KNOWN_HOSTS  (optional) pre-pinned known_hosts content; if set, used
#                    verbatim instead of ssh-keyscan (more secure / stable).
set -euo pipefail

: "${SSH_HOST:?SSH_HOST is required}"
PORT="${SSH_PORT:-65002}"

mkdir -p "${HOME}/.ssh"
chmod 700 "${HOME}/.ssh"
KNOWN="${HOME}/.ssh/known_hosts_hostinger"

STRICT="yes"
if [ -n "${SSH_KNOWN_HOSTS:-}" ]; then
  printf '%s\n' "${SSH_KNOWN_HOSTS}" > "${KNOWN}"
  echo "Using pinned HOSTINGER_SSH_KNOWN_HOSTS."
else
  echo "Scanning host key for ${SSH_HOST}:${PORT} ..."
  # Don't let a keyscan failure abort the script (set -e); handle it below.
  ssh-keyscan -T 10 -p "${PORT}" "${SSH_HOST}" > "${KNOWN}" 2>/dev/null || true
fi

if [ -s "${KNOWN}" ]; then
  chmod 600 "${KNOWN}"
  echo "Host key obtained — pinning with StrictHostKeyChecking=yes."
else
  # keyscan blocked/timed out — fall back to trust-on-first-use so the
  # deploy can still proceed. The actual auth is still key-based.
  STRICT="accept-new"
  echo "::warning::ssh-keyscan returned no key; falling back to StrictHostKeyChecking=accept-new."
  echo "Tip: pin the key by setting the HOSTINGER_SSH_KNOWN_HOSTS secret to remove this warning."
fi

# Exported for all later steps in this job.
echo "SSH_OPTS=-p ${PORT} -o UserKnownHostsFile=${KNOWN} -o StrictHostKeyChecking=${STRICT} -o ConnectTimeout=20" >> "${GITHUB_ENV}"
echo "SSH options ready (StrictHostKeyChecking=${STRICT})."
