#!/bin/bash

# Just a script that concatenates all of the files in js.s to js.js

OUTPUT="js.js"
if [ -f "$OUTPUT" ]; then
    rm "$OUTPUT"
fi
for JS in `find ./js.d -name "*.js" -exec echo {} \; | sort`; do
    cat $JS >> "$OUTPUT"
    echo ";" >> "$OUTPUT"
done
