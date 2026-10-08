#!/usr/bin/env bash
# Local WordPress for theme development: WordPress + SQLite (no MySQL needed) + WP-CLI.
# Usage: dev/setup.sh [PORT]   (default 8080). Re-running wipes the site and re-seeds it.
# The site lives in .wp/ (git-ignored); the theme folder is symlinked, so edits show up live.
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
PORT="${1:-8080}"
SITE="$REPO/.wp"
CACHE="${QD_WP_CACHE:-$HOME/.cache/qd-wp}"
WP="php $CACHE/wp-cli.phar --allow-root --path=$SITE"

mkdir -p "$CACHE"
[ -f "$CACHE/wp-cli.phar" ] || curl -sSL -o "$CACHE/wp-cli.phar" https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
[ -f "$CACHE/sqlite.zip" ] || curl -sSL -o "$CACHE/sqlite.zip" https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip
if [ ! -d "$CACHE/core" ]; then
  php "$CACHE/wp-cli.phar" --allow-root core download --locale=vi --path="$CACHE/core"
fi

rm -rf "$SITE"
cp -r "$CACHE/core" "$SITE"

# SQLite drop-in
unzip -q -o "$CACHE/sqlite.zip" -d "$SITE/wp-content/plugins/"
sed "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$SITE/wp-content/plugins/sqlite-database-integration#; s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" \
  "$SITE/wp-content/plugins/sqlite-database-integration/db.copy" > "$SITE/wp-content/db.php"

$WP config create --dbname=wp --dbuser=wp --dbpass=wp --skip-check --quiet \
  --extra-php <<PHP
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
PHP

$WP core install --url="http://localhost:$PORT" --title="Phòng khám Da liễu Thẩm mỹ Quang Đăng" \
  --admin_user=admin --admin_password=admin --admin_email=dev@example.com --skip-email --quiet

ln -sfn "$REPO/quangdang" "$SITE/wp-content/themes/quangdang"
$WP theme activate quangdang --quiet
$WP rewrite structure '/%postname%/' --quiet
$WP option update blogdescription "Viện thẩm mỹ quốc tế" --quiet
$WP eval-file "$REPO/dev/seed.php"
$WP rewrite flush --quiet

echo "Ready. Run: dev/serve.sh $PORT  →  http://localhost:$PORT  (admin / admin)"
