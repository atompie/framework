#!/bin/sh

find ../../src -type f \( -name "*.php" -o -name "*.mustache" \) -print >list
xgettext -j --from-code=UTF-8 --files-from=list --language=PHP --default-domain=default --keyword=Label
rm list