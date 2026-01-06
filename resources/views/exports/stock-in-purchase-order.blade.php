<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $orderNumber ?? 'PO-' . date('Ymd') }}</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
                font-size: 12px;
            }
            .page-break {
                page-break-before: always;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .company-info {
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 11px;
            color: #666;
        }
        
        .document-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            text-decoration: underline;
        }
        
        .supplier-info {
            margin-bottom: 20px;
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            min-width: 120px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .totals-section {
            margin-top: 30px;
        }
        
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin: 30px 0 10px 0;
            height: 40px;
        }
        
        .notes {
            margin-top: 20px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak
    </button>

    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">PT. MITRAJAYA SELARAS ABADI</div>
            <div class="company-address">
                LABORATORY & MEDICAL EQUIPMENT<br>
                Ruko Maison Avenue MA 10, Kota Wisata, Cibubur<br>
                Telp. / Fax : 82482412 , WA. 08119466470
            </div>
        </div>
        
        <div class="document-title">PURCHASE ORDER</div>
    </div>

    <!-- Supplier Information -->
    <div class="supplier-info">
        <div class="info-row">
            <span class="info-label">Kepada Yth:</span>
            <span>{{ $supplier->name ?? '-' }}</span>
        </div>
        @if($supplier->address)
            <div class="info-row">
                <span class="info-label">Alamat:</span>
                <span>{{ $supplier->address }}</span>
            </div>
        @endif
        @if($supplier->phone)
            <div class="info-row">
                <span class="info-label">Telepon:</span>
                <span>{{ $supplier->phone }}</span>
            </div>
        @endif
        @if($supplier->email)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span>{{ $supplier->email }}</span>
            </div>
        @endif
    </div>

    <!-- PO Information -->
    <div style="margin-bottom: 20px;">
        <div class="info-row">
            <span class="info-label">No. PO:</span>
            <span>{{ $orderNumber ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">No. Invoice:</span>
            <span>{{ $invoiceNumber ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal:</span>
            <span>{{ $transactionDate ? $transactionDate->format('d/m/Y') : '-' }}</span>
        </div>
    </div>

    <!-- Product Table -->
    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="35%">Nama Barang</th>
                <th class="text-center" width="15%">Kode</th>
                <th class="text-center" width="10%">Qty</th>
                <th class="text-center" width="15%">Harga Satuan</th>
                <th class="text-right" width="20%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockMovements as $index => $movement)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $movement->product->name }}</td>
                    <td class="text-center">{{ $movement->product->code }}</td>
                    <td class="text-center">{{ $movement->quantity }} {{ $movement->product->unit }}</td>
                    <td class="text-right">Rp {{ number_format($movement->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($movement->quantity * $movement->unit_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <table style="width: 300px; float: right;">
            <tr>
                <td style="border: 1px solid #333; padding: 8px; font-weight: bold;">Sub Total:</td>
                <td style="border: 1px solid #333; padding: 8px; text-align: right;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($includeTax)
                <tr>
                    <td style="border: 1px solid #333; padding: 8px; font-weight: bold;">PPN 11%:</td>
                    <td style="border: 1px solid #333; padding: 8px; text-align: right;">Rp {{ number_format($taxAmount, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td style="border: 1px solid #333; padding: 8px; font-weight: bold; background-color: #f0f0f0;">Total:</td>
                <td style="border: 1px solid #333; padding: 8px; text-align: right; font-weight: bold; background-color: #f0f0f0;">Rp {{ number_format($finalAmount, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <!-- Terbilang -->
    <div style="margin-top: 80px;">
        <strong>Terbilang:</strong> {{ ucwords(trim($terbilang)) }} rupiah
    </div>

    <!-- Notes -->
    @if($stockMovements->first()->notes)
        <div class="notes">
            <strong>Catatan:</strong><br>
            {{ $stockMovements->first()->notes }}
        </div>
    @endif

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Supplier,</div>
            <div class="signature-line"></div>
            <div>(_________________________)</div>
        </div>
        
        <div class="signature-box">
            <div>PT. Mitrajaya Selaras Abadi</div>
            <div class="signature-line"></div>
            <div>(_________________________)</div>
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 50px; text-align: center; font-size: 10px; color: #666;">
        Dokumen ini dicetak pada {{ $currentDate }}
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>
