<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ImportCustomersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, SkipsEmptyRows
{
    private $rowCount = 0;
    /**
     * @param array $row
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowCount++;

        return new Customer([
            'name' => $row['name'],
            'contact_person' => $row['contact_person'] ?? null,
            'contact_person_2' => $row['contact_person_2'] ?? null,
            'contact_person_3' => $row['contact_person_3'] ?? null,
            'phone' => $row['phone'] ?? null,
            'phone_2' => $row['phone_2'] ?? null,
            'phone_3' => $row['phone_3'] ?? null,
            'email' => $row['email'] ?? null,
            'address' => $row['address'] ?? null,
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'phone_3' => 'nullable|string|max:20',
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
        // Log error or handle silently
    }

    /**
     * Handle validation failures
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        // Handle validation failures
    }

    /**
     * Get row count
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
