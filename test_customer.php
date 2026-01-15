<?php

// Test parseIsActive method for customers
function parseIsActiveCustomer($value)
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

// Test data for customers
$testData = [
    ['name' => 'Customer 1', 'is_active' => true, 'notes' => 'Test active'],
    ['name' => 'Customer 2', 'is_active' => false, 'notes' => 'Test inactive'],
    ['name' => 'Customer 3', 'is_active' => 'TRUE', 'notes' => 'Test TRUE'],
    ['name' => 'Customer 4', 'is_active' => 'FALSE', 'notes' => 'Test FALSE'],
    ['name' => 'Customer 5', 'is_active' => 'yes', 'notes' => 'Test yes'],
    ['name' => 'Customer 6', 'is_active' => 'no', 'notes' => 'Test no'],
];

echo "Testing parseIsActive for CUSTOMERS:\n";
foreach ($testData as $index => $row) {
    $input = $row['is_active'];
    $output = parseIsActiveCustomer($input);

    echo "Input: ";
    var_export($input);
    echo " (type: " . gettype($input) . ") -> Output: " . ($output ? 'true' : 'false') . "\n";
}

echo "\n=== All tests completed successfully! ===\n";
