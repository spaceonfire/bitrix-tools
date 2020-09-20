#!/usr/bin/env bash
set -e

vendor/bin/apidoc -o docs/api --propertiesVisibility=public --methodsVisibility=public --methodsVisibility=protected

if [command -v npx &> /dev/null]; then
	npx prettier --write docs/**/*.md
else
	echo "Run manually 'npx prettier --write docs/**/*.md'"
fi
