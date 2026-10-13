#!/usr/bin/env bash
# cPanel deploy (run by .cpanel.yml from the repo root): copy quangdang/ into the live theme folder.
# The folder name depends on how the theme was first installed (quangdang, quangdang-main…), so every
# folder whose style.css is "Quang Đăng Clinic" is updated; with none, quangdang/ is created.
# The themes directory must already exist: a wrong path fails instead of creating a stray copy.
set -euo pipefail

THEMES="${1:?usage: cpanel-deploy.sh /path/to/wp-content/themes}"
if [ ! -d "$THEMES" ]; then
	echo "Deploy failed: $THEMES does not exist (is WordPress installed there?)" >&2
	exit 1
fi

targets=()
for dir in "$THEMES"/*/; do
	dir="${dir%/}"
	if [ -f "$dir/style.css" ] && grep -q "^Theme Name:[[:space:]]*Quang Đăng Clinic" "$dir/style.css"; then
		targets+=("$dir")
	fi
done
[ ${#targets[@]} -gt 0 ] || targets=("$THEMES/quangdang")

for dir in "${targets[@]}"; do
	mkdir -p "$dir"
	cp -Rf quangdang/. "$dir/"
	echo "Deployed theme to $dir"
done

# The host answers /favicon.ico with its own 404 before WordPress runs, so the file has to exist in the
# WordPress root. Only added when missing: an icon someone put there is never overwritten.
ROOT="$(cd "$THEMES/../.." && pwd)"
if [ -f "$ROOT/wp-config.php" ] || [ -f "$ROOT/wp-load.php" ]; then
	if [ ! -e "$ROOT/favicon.ico" ]; then
		cp quangdang/assets/images/brand/favicon.ico "$ROOT/favicon.ico"
		echo "Added $ROOT/favicon.ico"
	fi
fi
