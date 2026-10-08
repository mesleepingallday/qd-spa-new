#!/usr/bin/env bash
# Serve the local WordPress with PHP's built-in server.
set -euo pipefail
REPO="$(cd "$(dirname "$0")/.." && pwd)"
PORT="${1:-8080}"
exec php -S "localhost:$PORT" -t "$REPO/.wp" "$REPO/dev/router.php"
