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
                padding: 15px;
                font-size: 10px;
                line-height: 1.4;
            }
            .page-break {
                page-break-before: always;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.4;
            color: #000;
            background: white;
            max-width: 210mm;
            margin: 0 auto;
            padding: 10px;
        }
        
        .container {
            width: 100%;
            position: relative;
        }
        
        /* Compact letterhead */
        .letterhead {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        .company-logo {
            flex: 0 0 auto;
            margin-right: 20px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        
        .company-tagline {
            font-size: 9px;
            font-style: italic;
            margin-bottom: 4px;
        }
        
        .company-address {
            font-size: 8px;
            line-height: 1.2;
        }
        
        .company-legal {
            text-align: right;
            font-size: 8px;
            line-height: 1.2;
        }
        
        /* Document header */
        .doc-header {
            text-align: center;
            margin: 15px 0;
            position: relative;
        }
        
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        
        .doc-number {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 11px;
            font-weight: bold;
        }
        
        /* Compact info sections */
        .info-sections {
            display: flex;
            gap: 20px;
            margin: 15px 0;
        }
        
        .info-section {
            flex: 1;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        
        .info-item {
            font-size: 9px;
            margin-bottom: 2px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 60px;
        }
        
        /* Compact table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9px;
        }
        
        .items-table th {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            font-weight: bold;
            background: #f5f5f5;
            font-size: 8px;
        }
        
        .items-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
            font-size: 9px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        /* Compact totals */
        .totals-section {
            margin: 10px 0;
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2px;
            font-size: 9px;
        }
        
        .total-label {
            min-width: 80px;
            text-align: right;
            padding-right: 10px;
            font-weight: bold;
        }
        
        .total-value {
            min-width: 100px;
            text-align: right;
            font-weight: bold;
        }
        
        .grand-total {
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
            font-size: 10px;
        }
        
        /* Compact terbilang */
        .terbilang-section {
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #ccc;
            background: #f9f9f9;
            font-size: 9px;
        }
        
        /* Compact signatures */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
        }
        
        .signature-box {
            text-align: center;
            width: 30%;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 25px;
            font-size: 9px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 25px;
            margin-bottom: 3px;
        }
        
        .signature-name {
            font-size: 8px;
        }
        
        /* Compact footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 7px;
            color: #666;
        }
        
        /* Print button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-family: Arial, sans-serif;
        }
        
        .print-button:hover {
            background: #34495e;
        }
        
        /* Notes */
        .notes-section {
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #ccc;
            font-size: 8px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        /* Terms */
        .terms-section {
            margin: 10px 0;
            font-size: 8px;
        }
        
        .terms-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .terms-list {
            list-style-type: decimal;
            margin-left: 15px;
        }
        
        .terms-list li {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print PO</button>
    
    <div class="container">
        <!-- Letterhead -->
        <div class="letterhead">
            <div class="company-logo">
                <img src="{{ asset('public/images/logo.png') }}" alt="PT. MITRAJAYA SELARAS ABADI" style="max-height: 80px;">
            </div>
            <div class="company-info">
                <div class="company-name">PT. MITRAJAYA SELARAS ABADI</div>
                <div class="company-tagline">Laboratory & Medical Equipment</div>
                <div class="company-address">
                    Ruko Maison Avenue MA 19, Kota Wisata, Cibubur<br>
                    Telp: 82482412 | WA: 08119466470<br>
                    Email: info@mitrajayaselarasabadi.com
                </div>
            </div>
        </div>

        <!-- Document Header -->
        <div class="doc-header">
            <div class="doc-title">Purchase Order</div>
            <div class="doc-subject">Subject: Pemesanan Barang</div>
            <div class="doc-number">No: {{ $orderNumber ?? 'PO-' . date('Ymd') }}</div>
        </div>

        <!-- Date and Recipient -->
        <div class="info-sections">
            <div class="info-section">
                <div class="section-title">Kepada:</div>
                <div class="info-item">
                    {{ $supplier->name ?? '-' }}
                </div>
                @if($supplier->address)
                    <div class="info-item">
                        {{ $supplier->address }}
                    </div>
                @endif
            </div>
            
            <div class="info-section text-right">
                <div class="info-item">
                    Bogor, {{ $transactionDate ? $transactionDate->format('d F Y') : date('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Formal Request Text Before Table -->
        <div class="notes-section">
            <p>Dengan hormat,<br>
            Mohon disediakan pemesanan barang-barang tersebut di bawah ini:</p>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Nama Barang</th>
                    <th width="10%">HARGA/BOX TERMASUK PPN</th>
                    <th width="10%">DISC</th>
                    <th width="10%">QTY</th>
                    <th width="20%">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockMovements as $index => $movement)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $movement->product->name }}{{ $movement->product->description ? ' - ' . $movement->product->description : '' }}</td>
                        <td class="text-right">Rp {{ number_format($movement->unit_price, 0, ',', '.') }}</td>
                        <td class="text-center">{{ ($movement->discount ?? 0) }}%</td>
                        <td class="text-center">{{ $movement->quantity }} {{ $movement->product->unit }}</td>
                        <td class="text-right">Rp {{ number_format($movement->quantity * $movement->unit_price * (1 - (($movement->discount ?? 0) / 100)), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span class="total-value">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            @if($includeTax)
                <div class="total-row">
                    <span class="total-label">PPN 11%:</span>
                    <span class="total-value">Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-row grand-total">
                <span class="total-label">Total:</span>
                <span class="total-value">Rp {{ number_format($finalAmount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Formal Request Text After Table -->
        <div class="notes-section">
            <p>Demikian permintaan barang dari kami, mohon segera disediakan, terimakasih atas kerjasamanya.</p>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Hormat kami,</div>
                <div class="signature-line">
                    <img src="{{ asset('images/signature.png') }}" alt="" style="max-height: 40px;">
                </div>
                <div class="signature-name">(Yayuk P. Wardani)</div>
                <div class="signature-title">Purchasing</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>PT. MITRAJAYA SELARAS ABADI</div>
            <div>Ruko Maison Avenue MA 19, Kota Wisata, Cibubur</div>
            <div>Dicetak pada {{ $currentDate }}</div>
        </div>
    </div>
</body>
</html>
