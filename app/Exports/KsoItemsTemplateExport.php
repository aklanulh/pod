<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class KsoItemsTemplateExport implements FromArray, WithHeadings, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'item_type',
            'main_item_no_registrasi',
            'customer_name',
            'nama_alat',
            'brand',
            'model',
            'serial_number',
            'no_registrasi',
            'kategori',
            'nilai_alat_utama',
            'butuh_komputer',
            'keterangan',
            'spesifikasi_teknis',
            'kondisi',
            'lokasi_penempatan',
            'pic_customer',
            'pic_msa',
            'tanggal_investasi',
            'tanggal_install',
            'tanggal_deployment',
            'garansi_mulai',
            'garansi_berakhir',
            'periode_kso_mulai',
            'periode_kso_berakhir',
            'durasi_kso_bulan',
            'status'
        ];
    }

    public function title(): string
    {
        return 'KSO Items Template';
    }
}
