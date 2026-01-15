<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;

class ImportCustomersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, SkipsEmptyRows
{
    private $rowCount = 0;
    private $skippedRows = 0;
    /**
     * Clean Excel formula from value
     */
    private function cleanFormula($value)
    {
        if (empty($value)) {
            return null;
        }

        // Check if it's an Excel formula
        if (is_string($value) && str_starts_with($value, '=')) {
            // Return null for formulas since we can't evaluate them
            return null;
        }

        return $value;
    }

    /**
     * @param array $row
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            $this->rowCount++;

            return new Customer([
                'name' => $row['name'],
                'contact_person' => $this->cleanFormula($row['contact_person']),
                'contact_person_2' => $this->cleanFormula($row['contact_person_2']),
                'contact_person_3' => $this->cleanFormula($row['contact_person_3']),
                'phone' => $this->cleanFormula($row['phone']),
                'phone_2' => $this->cleanFormula($row['phone_2']),
                'phone_3' => $this->cleanFormula($row['phone_3']),
                'email' => $this->cleanFormula($row['email']),
                'address' => $this->cleanFormula($row['address']),
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing customer row: ' . json_encode($row));
            Log::error('Error message: ' . $e->getMessage());
            return null; // Skip this row
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'phone_2' => 'nullable|string|max:255',
            'phone_3' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_2' => 'nullable|string|max:255',
            'contact_person_3' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Nama customer wajib diisi',
            'name.string' => 'Nama customer harus berupa string',
            'email.email' => 'Format email tidak valid',
        ];
    }

    /**
     * Handle errors
     */
    public function onError(\Throwable $e)
    {
        Log::error('ImportCustomersImport Error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
    }

    /**
     * Handle validation failures
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->skippedRows++;
            Log::error('ImportCustomersImport Validation Failure:');
            Log::error('Row: ' . $failure->row());
            Log::error('Attribute: ' . $failure->attribute());
            Log::error('Errors: ' . implode(', ', $failure->errors()));
            Log::error('Values: ' . json_encode($failure->values()));
        }
    }

    /**
     * Get row count
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * Get skipped rows count
     */
    public function getSkippedRowsCount(): int
    {
        return $this->skippedRows;
    }

    /**
     * Get total processed rows (successful + skipped)
     */
    public function getTotalProcessedRows(): int
    {
        return $this->rowCount + $this->skippedRows;
    }
}
