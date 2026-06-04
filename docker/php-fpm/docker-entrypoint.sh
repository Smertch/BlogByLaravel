#!/bin/bash
# Normalize CRLF to LF in PHP files on container start (avoids parse errors when the parser expects LF).
set -e
if [ -d /application ]; then
  find /application -type f -name "*.php" \
    -not -path "*/vendor/*" \
    -not -path "*/node_modules/*" \
    2>/dev/null | while read -r f; do
    if grep -q $'\r' "$f" 2>/dev/null; then
      sed -i 's/\r$//' "$f"
    fi
  done
fi
exec "$@"
