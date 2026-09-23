#!/usr/bin/env bash

set -euo pipefail

root_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
plugin_dir="$(basename "$root_dir")"
plugin_file="$root_dir/bricks-elements-pack.php"
output_dir="${1:-$root_dir/dist}"

if [[ ! -f "$plugin_file" ]]; then
    printf 'Plugin file not found: %s\n' "$plugin_file" >&2
    exit 1
fi

version="$(awk -F ': ' '/^[[:space:]]*\* Version:/{print $2; exit}' "$plugin_file")"
if [[ -z "$version" ]]; then
    printf 'Unable to determine the plugin version.\n' >&2
    exit 1
fi

mkdir -p "$output_dir"

archive="$output_dir/$plugin_dir-$version.zip"
rm -f "$archive"

if command -v zip >/dev/null; then
    (
        cd "$(dirname "$root_dir")"
        zip -rq "$archive" "$plugin_dir" \
        -x "$plugin_dir/.git/*" \
        -x "$plugin_dir/dist/*" \
        -x "$plugin_dir/.slim/*" \
        -x "$plugin_dir/*.zip" \
            -x "$plugin_dir/AGENTS.md" \
            -x '*/.DS_Store' \
            -x '*/Thumbs.db'
    )
elif command -v python3 >/dev/null; then
    ROOT_DIR="$root_dir" ARCHIVE="$archive" python3 - <<'PY'
from pathlib import Path
from zipfile import ZIP_DEFLATED, ZipFile
import os

root = Path(os.environ['ROOT_DIR'])
archive = Path(os.environ['ARCHIVE'])
excluded_directories = {'.git', 'dist', '.slim'}
excluded_names = {'AGENTS.md', '.DS_Store', 'Thumbs.db'}

with ZipFile(archive, 'w', ZIP_DEFLATED) as package:
    for path in root.rglob('*'):
        relative_path = path.relative_to(root)
        if (
            not path.is_file()
            or any(part in excluded_directories for part in relative_path.parts)
            or path.name in excluded_names
            or path.suffix == '.zip'
        ):
            continue
        package.write(path, path.relative_to(root.parent))
PY
else
    printf 'Install zip or Python 3 to create a plugin archive.\n' >&2
    exit 1
fi

printf 'Created %s\n' "$archive"
