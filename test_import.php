<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\Supplier;
use App\Imports\ImportSuppliersImport;

// Test data
$testData = [
    ['name' => 'Test Supplier 1', 'is_active' => true, 'notes' => 'Test active'],
    ['name' => 'Test Supplier 2', 'is_active' => false, 'notes' => 'Test inactive'],
    ['name' => 'Test Supplier 3', 'is_active' => 'TRUE', 'notes' => 'Test TRUE'],
    ['name' => 'Test Supplier 4', 'is_active' => 'FALSE', 'notes' => 'Test FALSE'],
    ['name' => 'Test Supplier 5', 'is_active' => 'yes', 'notes' => 'Test yes'],
    ['name' => 'Test Supplier 6', 'is_active' => 'no', 'notes' => 'Test no'],
];

echo "Testing parseIsActive method:\n";
$import = new ImportSuppliersImport();

foreach ($testData as $index => $row) {
    echo "Row " . ($index + 1) . ": " . json_encode($row) . "\n";

    // Test parseIsActive method
    $reflection = new ReflectionClass($import);
    $method = $reflection->getMethod('parseIsActive');
    $method->setAccessible(true);

    $parsedValue = $method->invoke($import, $row['is_active']);
    echo "Parsed: " . ($parsedValue ? 'true' : 'false') . "\n";

    // Test model creation
    try {
        $model = $import->model($row);
        if ($model) {
            echo "Model created: " . $model->name . " - is_active: " . ($model->is_active ? 'true' : 'false') . "\n";
        } else {
            echo "Model creation failed\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "---\n";
}

echo "\nTesting direct database save:\n";
foreach ($testData as $index => $row) {
    try {
        $supplier = new Supplier([
            'name' => $row['name'],
            'is_active' => $row['is_active'],
            'notes' => $row['notes']
        ]);
        $supplier->save();
        echo "Direct save: " . $supplier->name . " - is_active: " . ($supplier->is_active ? 'true' : 'false') . "\n";
    } catch (Exception $e) {
        echo "Direct save error: " . $e->getMessage() . "\n";
    }
}
