<?php

// Debug boolean handling
function debugBoolean($value)
{
    echo "Input: ";
    var_export($value);
    echo " (type: " . gettype($value) . ")\n";

    if (empty($value)) {
        echo "  -> empty() returns true\n";
        return true;
    }

    if (is_bool($value)) {
        echo "  -> is_bool() returns true\n";
        echo "  -> returning value as-is: " . ($value ? 'true' : 'false') . "\n";
        return $value;
    }

    if (is_string($value)) {
        echo "  -> is_string() returns true\n";
        $trimmed = trim($value);
        echo "  -> trim() result: ";
        var_export($trimmed);
        echo "\n";
        $lowered = strtolower($trimmed);
        echo "  -> strtolower() result: ";
        var_export($lowered);
        echo "\n";
        $inArray = in_array($lowered, ['yes', 'y', 'true', '1', 'active', 'aktif', 'on']);
        echo "  -> in_array() result: " . ($inArray ? 'true' : 'false') . "\n";
        return $inArray;
    }

    if (is_numeric($value)) {
        echo "  -> is_numeric() returns true\n";
        $cast = (bool)$value;
        echo "  -> (bool) cast result: " . ($cast ? 'true' : 'false') . "\n";
        return $cast;
    }

    echo "  -> default: returning true\n";
    return true;
}

echo "=== Testing boolean false ===\n";
$result1 = debugBoolean(false);
echo "Result: " . ($result1 ? 'true' : 'false') . "\n\n";

echo "=== Testing boolean (string) 'false' ===\n";
$result2 = debugBoolean('false');
echo "Result: " . ($result2 ? 'true' : 'false') . "\n\n";

echo "=== Testing empty string ===\n";
$result3 = debugBoolean('');
echo "Result: " . ($result3 ? 'true' : 'false') . "\n\n";

echo "=== Testing null ===\n";
$result4 = debugBoolean(null);
echo "Result: " . ($result4 ? 'true' : 'false') . "\n\n";
