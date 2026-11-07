#!/usr/bin/env bash
set -euo pipefail

##
## Comprueba si existe una variable=valor en un archivo o la añade de forma segura.
## Soporta caracteres especiales ($, !, *, @, /, &, |) sin corrupción por sed.
##
replace_or_add_var_in_file() {
    local FILE="${1}"
    local VAR="${2}"
    local VALUE_RAW="${3:-}"

    if [[ -z "${VALUE_RAW}" ]]; then
        return 0
    fi

    python3 - "${FILE}" "${VAR}" "${VALUE_RAW}" <<'PYEOF'
import re
import sys
from pathlib import Path

file_path = Path(sys.argv[1])
var_name = sys.argv[2]
raw_val = sys.argv[3]

# Envolver entre comillas dobles si contiene espacios o caracteres especiales de .env
val = raw_val.strip()
if not (val.startswith('"') and val.endswith('"')):
    if any(ch in val for ch in (' ', '$', '!', '*', '@', '#', ';', '|')):
        escaped = val.replace('\\', '\\\\').replace('"', '\\"').replace('$', '\\$')
        val = f'"{escaped}"'

lines = file_path.read_text(encoding='utf-8').splitlines()
pattern = re.compile(rf'^\s*#?\s*{re.escape(var_name)}\s*=.*$')

replaced = False
new_lines = []
for line in lines:
    if not replaced and pattern.match(line):
        new_lines.append(f'{var_name}={val}')
        replaced = True
    else:
        new_lines.append(line)

if not replaced:
    new_lines.append(f'{var_name}={val}')

file_path.write_text('\n'.join(new_lines) + '\n', encoding='utf-8')
PYEOF
}
