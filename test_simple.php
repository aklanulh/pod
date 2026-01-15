<?php

// Test parseIsActive method directly
function parseIsActive($value)
{
    // Handle boolean values directly FIRST (before empty check)
    if (is_bool($value)) {
        return $value; // Return boolean as-is
    }

    if (is_null($value) || $value === '') {
        return true; // Default to active
    }

    // Handle string representations
    if (is_string($value)) {
        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'y', 'true', '1', 'active', 'aktif', 'on']);
    }

    // Handle numeric values
    if (is_numeric($value)) {
        return (bool)$value;
    }

    return true; // Default to active
}

// Test data
$testData = [
    ['name' => 'Test 1', 'is_active' => true],
    ['name' => 'Test 2', 'is_active' => false],
    ['name' => 'Test 3', 'is_active' => 'TRUE'],
    ['name' => 'Test 4', 'is_active' => 'FALSE'],
    ['name' => 'Test 5', 'is_active' => 'yes'],
    ['name' => 'Test 6', 'is_active' => 'no'],
];

echo "Testing parseIsActive function:\n";
foreach ($testData as $index => $row) {
    $input = $row['is_active'];
    $output = parseIsActive($input);

    echo "Input: ";
    var_export($input);
    echo " (type: " . gettype($input) . ") -> Output: " . ($output ? 'true' : 'false') . "\n";
}
