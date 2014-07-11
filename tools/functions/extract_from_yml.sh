#! /bin/bash

# "Parse" the YAML file for the given parameter.
extract_from_yml() {
    local path="$1"
    local parameter="$2";

    while read -r line; do
        if ! [[ "$line" =~ ^[[:space:]]*([^[:space:]:]*)[[:space:]]*:[[:space:]]*([^[:space:]]*) ]]; then
            continue;
        fi;

        local curr_parameter="${BASH_REMATCH[1]}";
        local curr_value="${BASH_REMATCH[2]}";
        if [ "$curr_parameter" = "$parameter" ]; then
            echo "$curr_value";
            return;
        fi;
    done < "$path";
    return 1;
}
