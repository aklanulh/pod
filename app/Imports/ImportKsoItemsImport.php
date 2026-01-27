<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\KsoItem;
use App\Models\KsoSupportItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportKsoItemsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, SkipsEmptyRows
{
    private $rowCount = 0;
    private $skippedRows = 0;
    private $supportItemsData = [];

    /**
     * Clean Excel formula from value
     */
    private function cleanFormula($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value) && str_starts_with($value, '=')) {
            return null;
        }

        return $value;
    }

    /**
     * Parse date value from various formats
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Handle Excel serial number format
            if (is_numeric($value) && $value > 25569) {
                $date = Carbon::createFromTimestamp(($value - 25569) * 86400);
                return $date->format('Y-m-d');
            }

            // Handle string date formats
            if (is_string($value)) {
                $value = trim($value);

                // Try common date formats
                $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d', 'd-m-Y', 'm-d-Y'];

                foreach ($formats as $format) {
                    try {
                        return Carbon::createFromFormat($format, $value)->format('Y-m-d');
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                // Try Carbon's flexible parser
                try {
                    return Carbon::parse($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }

            return $value;
        } catch (\Exception $e) {
            Log::warning('Date parsing failed for value: ' . $value . ' - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse decimal value
     */
    private function parseDecimal($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Remove commas and convert to float
        $cleaned = str_replace(',', '', $value);
        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    /**
     * Parse boolean value
     */
    private function parseBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_null($value) || $value === '') {
            return false;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['yes', 'y', 'true', '1', 'active', 'aktif', 'on', 'ya']);
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return false;
    }

    /**
     * Parse status value
     */
    private function parseStatus($value)
    {
        if (empty($value)) {
            return 'active';
        }

        $value = strtolower(trim($value));
        return in_array($value, ['inactive', 'non-active', 'tidak aktif', 'off']) ? 'inactive' : 'active';
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            $this->rowCount++;

            // Debug: Log the raw row data
            Log::info('Processing row ' . $this->rowCount . ': ' . json_encode($row));

            // Skip if it's a support item row (handled separately)
            if (isset($row['item_type']) && strtolower($row['item_type']) === 'support') {
                Log::info('Row identified as support item, storing for later processing');
                $this->storeSupportItem($row);
                return null;
            }

            // Check if customer_name is empty for main items
            if (empty($row['customer_name'])) {
                Log::warning('Customer name is empty for main item at row ' . $this->rowCount);
                $this->skippedRows++;
                return null;
            }

            // Find customer - use flexible search
            $customerName = trim($row['customer_name'] ?? '');
            $customer = Customer::where('name', $customerName)
                ->orWhere('name', 'LIKE', '%' . $customerName . '%')
                ->first();

            if (!$customer) {
                Log::warning('Customer not found: "' . $customerName . '" at row ' . $this->rowCount);
                $this->skippedRows++;
                return null;
            }

            // Parse financial values
            $nilaiAlatUtama = $this->parseDecimal($row['nilai_alat_utama'] ?? 0);

            // Parse dates dengan fallback yang aman
            $deploymentDate = $this->parseDate($row['tanggal_deployment'] ?? $row['tanggal_install'] ?? null);
            $investmentDate = $this->parseDate($row['tanggal_investasi'] ?? $deploymentDate);

            // Parse KSO period dates
            $periodeKsoMulai = $this->parseDate($row['periode_kso_mulai'] ?? $deploymentDate);
            $periodeKsoBerakhir = $this->parseDate($row['periode_kso_berakhir']);

            // If periode_kso_berakhir is not provided but durasi_kso_bulan is provided, calculate it
            if (!$periodeKsoBerakhir && $periodeKsoMulai && isset($row['durasi_kso_bulan'])) {
                $durationMonths = (int) ($row['durasi_kso_bulan'] ?? 36);
                $periodeKsoBerakhir = Carbon::parse($periodeKsoMulai)->addMonths($durationMonths)->toDateString();
            }

            Log::info('Creating KSO item for customer: ' . $customer->name . ' at row ' . $this->rowCount);

            return new KsoItem([
                'customer_id' => $customer->id,
                'nama_alat' => $row['nama_alat'],
                'brand' => $this->cleanFormula($row['brand'] ?? ''),
                'model' => $this->cleanFormula($row['model'] ?? ''),
                'serial_number' => $this->cleanFormula($row['serial_number'] ?? ''),
                'no_registrasi' => $this->cleanFormula($row['no_registrasi'] ?? ''),
                'kategori' => $this->cleanFormula($row['kategori'] ?? ''),
                'nilai_alat_utama' => $nilaiAlatUtama,
                'butuh_komputer' => $this->parseBoolean($row['butuh_komputer'] ?? false),
                'total_pendukung' => 0, // Will be calculated after support items are imported
                'total_investasi' => $nilaiAlatUtama, // Will be updated after support items are imported
                'keterangan' => $this->cleanFormula($row['keterangan'] ?? ''),
                'spesifikasi_teknis' => $this->cleanFormula($row['spesifikasi_teknis'] ?? ''),
                'kondisi' => $this->cleanFormula($row['kondisi'] ?? 'baik'),
                'lokasi_penempatan' => $this->cleanFormula($row['lokasi_penempatan'] ?? ''),
                'pic_customer' => $this->cleanFormula($row['pic_customer'] ?? ''),
                'pic_msa' => $this->cleanFormula($row['pic_msa'] ?? ''),
                'tanggal_investasi' => $investmentDate,
                'tanggal_install' => $deploymentDate,
                'garansi_mulai' => $this->parseDate($row['garansi_mulai'] ?? ''),
                'garansi_berakhir' => $this->parseDate($row['garansi_berakhir'] ?? ''),
                'periode_kso_mulai' => $periodeKsoMulai,
                'periode_kso_berakhir' => $periodeKsoBerakhir,
                'durasi_kso_bulan' => (int) ($row['durasi_kso_bulan'] ?? 36),
                'status' => $this->parseStatus($row['status'] ?? 'active'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing KSO item row ' . $this->rowCount . ': ' . json_encode($row));
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            $this->skippedRows++;
            return null;
        }
    }

    /**
     * Store support item data temporarily
     */
    private function storeSupportItem(array $row)
    {
        // Store support items data to be processed after main items are created
        $this->supportItemsData[] = $row;
    }

    /**
     * Process support items after all main items are imported
     */
    public function afterImport()
    {
        try {
            foreach ($this->supportItemsData as $supportData) {
                // Cari alat utama berdasarkan no_registrasi
                $mainItemNoRegistrasi = $supportData['main_item_no_registrasi'] ?? '';
                if (!empty($mainItemNoRegistrasi)) {
                    $mainItem = KsoItem::where('no_registrasi', $mainItemNoRegistrasi)->first();
                    if ($mainItem) {
                        KsoSupportItem::create([
                            'kso_item_id' => $mainItem->id,
                            'nama_item' => $supportData['nama_alat'] ?? $supportData['nama_item'] ?? '',
                            'nilai_item' => $this->parseDecimal($supportData['nilai_alat_utama'] ?? $supportData['nilai_item'] ?? 0),
                            'brand' => $this->cleanFormula($supportData['brand'] ?? ''),
                            'model' => $this->cleanFormula($supportData['model'] ?? ''),
                            'serial_number' => $this->cleanFormula($supportData['serial_number'] ?? ''),
                            'no_registrasi' => $this->cleanFormula($supportData['no_registrasi'] ?? ''),
                            'garansi_berakhir' => $this->parseDate($supportData['garansi_berakhir'] ?? ''),
                            'kondisi' => $this->cleanFormula($supportData['kondisi'] ?? 'baik'),
                            'status' => $this->parseStatus($supportData['status'] ?? 'active'),
                            'spesifikasi' => $this->cleanFormula($supportData['spesifikasi_teknis'] ?? ''),
                        ]);
                    } else {
                        Log::warning("Main item with no_registrasi '{$mainItemNoRegistrasi}' not found for support item: " . ($supportData['nama_alat'] ?? $supportData['nama_item'] ?? 'Unknown'));
                    }
                } else {
                    Log::warning("Support item '" . ($supportData['nama_alat'] ?? $supportData['nama_item'] ?? 'Unknown') . "' has no main_item_no_registrasi specified");
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing support items: ' . $e->getMessage());
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'item_type' => 'required|in:main,support',
            'main_item_no_registrasi' => 'nullable|string|max:255',
            'customer_name' => 'required_if:item_type,main|string|max:255',
            'nama_alat' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'no_registrasi' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'nilai_alat_utama' => 'nullable|numeric|min:0',
            'butuh_komputer' => 'nullable',
            'keterangan' => 'nullable|string',
            'spesifikasi_teknis' => 'nullable|string',
            'kondisi' => 'nullable|string|max:255',
            'lokasi_penempatan' => 'nullable|string|max:255',
            'pic_customer' => 'nullable|string|max:255',
            'pic_msa' => 'nullable|string|max:255',
            'tanggal_investasi' => 'nullable',
            'tanggal_install' => 'nullable',
            'tanggal_deployment' => 'nullable',
            'garansi_mulai' => 'nullable',
            'garansi_berakhir' => 'nullable',
            'periode_kso_mulai' => 'nullable',
            'periode_kso_berakhir' => 'nullable',
            'durasi_kso_bulan' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive'
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'customer_name.required' => 'Nama customer wajib diisi',
            'nama_alat.required' => 'Nama alat wajib diisi',
            'nilai_alat_utama.numeric' => 'Nilai alat utama harus berupa angka',
            'tanggal_investasi.date' => 'Format tanggal investasi tidak valid',
            'durasi_kso_bulan.min' => 'Durasi KSO minimal 1 bulan',
            'status.in' => 'Status harus berupa: active atau inactive',
        ];
    }

    /**
     * Handle errors
     */
    public function onError(\Throwable $e)
    {
        Log::error('ImportKsoItemsImport Error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
    }

    /**
     * Handle validation failures
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->skippedRows++;
            Log::error('ImportKsoItemsImport Validation Failure at row ' . $failure->row());
            Log::error('Attribute: ' . $failure->attribute());
            Log::error('Errors: ' . implode(', ', $failure->errors()));
            Log::error('Values: ' . json_encode($failure->values()));

            // Check for common issues
            $values = $failure->values();
            if (isset($values['item_type']) && $values['item_type'] === 'main') {
                if (empty($values['customer_name'])) {
                    Log::error('Main item missing customer_name at row ' . $failure->row());
                }
                if (empty($values['nama_alat'])) {
                    Log::error('Main item missing nama_alat at row ' . $failure->row());
                }
            }
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
