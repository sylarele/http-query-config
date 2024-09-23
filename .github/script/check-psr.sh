output=$(composer dumpautoload --strict-psr 2>&1)

if echo "$output" | grep -q "does not comply with psr-"; then
    echo "$output" | grep "does not comply with psr-"
    exit 1
else
    echo "No PSR compliance errors found."
    exit 0
fi
