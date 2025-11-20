<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur - {{ $invoiceNumber }}</title>
    <style>
        @page {
            size: A4;
            margin: 8mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 30px;
            line-height: 1;
            margin: 0;
            padding: 20px;
            color: #000;
            background-color: #f5f5f5;
        }
        
        .invoice-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            border: 2px solid #000;
        }
        
        .header {
            border: 2px solid #000;
            border-bottom: 1px solid #000;
            padding: 8px 8px 8px 8px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }
        
        .logo {
            width: 80px;
            height: 60px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
            color: #000;
        }
        
        .company-subtitle {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #000;
        }
        
        .company-address {
            font-size: 11px;
            line-height: 1.2;
            color: #000;
        }
        
        .header-right {
            text-align: right;
            font-size: 13px;
            flex: 1;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .date-info {
            margin-bottom: 5px;
        }
        
        
        .faktur-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            padding: 4px 0;
            margin: 4px 0;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            font-size: 10px;
            border: 1px solid #000;
            margin-bottom: 0;
        }
        
        .invoice-left {
            width: 50%;
        }
        
        .invoice-right {
            width: 45%;
            text-align: right;
        }
        
        .detail-row {
            margin-bottom: 1px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            border: 1px solid #000;
        }
        
        .products-table th {
            background-color: #e8e8e8;
            border: none;
            padding: 3px 2px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            height: 18px;
        }
        
        .products-table td {
            border: none;
            padding: 2px 3px;
            text-align: center;
            font-size: 12px;
            height: 16px;
            vertical-align: middle;
        }
        
        .center-cell {
            text-align: center;
            padding: 2px 3px;
        }
        
        .products-table .text-left {
            text-align: left;
        }
        
        .products-table .text-right {
            text-align: right;
        }
        
        .currency-cell {
            text-align: right;
            padding-right: 8px;
            white-space: nowrap;
        }
        
        .currency-rp {
            display: inline-block;
            width: 20px;
            text-align: left;
        }
        
        .currency-amount {
            display: inline-block;
            text-align: right;
            min-width: 80px;
        }
        
        .totals-section {
            margin-top: -35px;
            display: flex;
            justify-content: flex-end;
        }
        
        .totals-table {
            border-collapse: collapse;
            width: 250px;
            border: 1px solid #000;
        }
        
        .totals-table td {
            border: none !important;
            padding: 3px 5px;
            font-size: 11px;
        }
        
        .totals-table tr {
            border: none !important;
        }
        
        .totals-table .label {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            width: 100px;
            border: none !important;
        }
        
        .totals-table .amount {
            text-align: right;
            font-weight: bold;
            padding-right: 8px;
            white-space: nowrap;
            border: none !important;
        }
        
        .footer-section {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            min-height: 80px;
        }
        
        .footer-left, .footer-center, .footer-right {
            width: 30%;
        }
        
        .footer-center {
            text-align: center;
        }
        
        .footer-right {
            text-align: right;
        }
        
        .terbilang {
            margin-top: 8px;
            font-size: 10px;
        }
        
        .bank-info {
            margin-top: 5px;
            font-size: 10px;
            line-height: 1.2;
        }
        
        .signature-area {
            margin-top: 50px;
            min-height: 40px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }
        
        @media print {
            body { 
                margin: 0; 
                padding: 0;
                background-color: white !important;
            }
            .invoice-container { 
                border: none; 
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="{{ asset('public/images/logo.png') }}" alt="PT MSA Logo">
            </div>
            <div class="company-info">
                <div class="company-name">PT. MITRAJAYA SELARAS ABADI</div>
                <div class="company-subtitle">LABORATORY & MEDICAL EQUIPMENT</div>
                <div class="company-address">
                    Ruko Maison Avenue MA 19, Kota Wisata, Cibubur<br>
                    Telp. / Fax : 82482412 , WA. 08119466470
                </div>
            </div>
            <div class="header-right">
                <div class="date-info" style="text-align: right; margin-bottom: 5px;">
                    Bogor, {{ $currentDate }}<br>
                    Kepada Yth,<br>
                    <strong>{{ $customerName }}</strong>
                </div>
                <div style="text-align: right; align-self: flex-end; max-width: 200px;">
                    @if($customer && $customer->address)
                        {!! nl2br(e($customer->address)) !!}
                    @else
                        Alamat tidak tersedia
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Faktur Title -->
        <div class="faktur-title">FAKTUR</div>
        
        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="invoice-left">
                <div class="detail-row"><strong>No. Faktur : {{ $invoiceNumber }}</strong></div>
            </div>
            <div class="invoice-right">
                <div class="detail-row"><strong>Cara Pembayaran : Tempo {{ $paymentTerms ?? 30 }} hari</strong></div>
                <div class="detail-row"><strong>Tgl Jatuh Tempo : {{ now()->addDays($paymentTerms ?? 30)->format('d/m/Y') }}</strong></div>
            </div>
        </div>
        
        <!-- Products Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 180px;">Nama Barang</th>
                    <th style="width: 70px;">P.Number</th>
                    <th style="width: 70px;">Harga Satuan</th>
                    <th style="width: 50px;">Banyaknya</th>
                    <th style="width: 35px;">Disc</th>
                    <th style="width: 70px;">Harga Netto</th>
                    <th style="width: 70px;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartData as $index => $item)
                    @php
                        $product = \App\Models\Product::find($item['product_id']);
                        $totalPrice = $item['quantity'] * $item['unit_price'];
                        $discountPercent = $item['discount'] ?? 0;
                        $discountAmount = $totalPrice * ($discountPercent / 100);
                        $nettoAmount = $totalPrice - $discountAmount;
                        $unit = $product ? $product->unit : 'pcs';
                    @endphp
                    <tr>
                        <td class="center-cell">{{ $index + 1 }}</td>
                        <td class="text-left">{{ $product ? $product->name : 'Unknown Product' }}</td>
                        <td class="center-cell">{{ $product ? $product->code : '' }}</td>
                        <td class="currency-cell"><span class="currency-rp">Rp</span><span class="currency-amount">{{ number_format($item['unit_price'], 0, ',', '.') }}</span></td>
                        <td class="center-cell">{{ $item['quantity'] }} {{ $unit }}</td>
                        <td class="center-cell">{{ $discountPercent }}%</td>
                        <td class="currency-cell"><span class="currency-rp">Rp</span><span class="currency-amount">{{ number_format($item['unit_price'] * (1 - $discountPercent/100), 0, ',', '.') }}</span></td>
                        <td class="currency-cell"><span class="currency-rp">Rp</span><span class="currency-amount">{{ number_format($nettoAmount, 0, ',', '.') }}</span></td>
                    </tr>
                @endforeach
                
                <!-- Empty rows to fill space -->
                @for($i = count($cartData); $i < 6; $i++)
                    <tr>
                        <td class="center-cell">&nbsp;</td>
                        <td class="text-left">&nbsp;</td>
                        <td class="center-cell">&nbsp;</td>
                        <td class="currency-cell">&nbsp;</td>
                        <td class="center-cell">&nbsp;</td>
                        <td class="center-cell">&nbsp;</td>
                        <td class="currency-cell">&nbsp;</td>
                        <td class="currency-cell">&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>
        
        <!-- PO Number Section -->
        <div style="margin: 0px 10px 0 10px; padding: 4px; font-size: 10px;">
            <strong>PO No : {{ $orderNumber ?: '00535/PO/PMC/06/2025' }}</strong>
        </div>
        
        <!-- Terbilang -->
        <div style="margin: -2px 10px; font-size: 10px; display: flex; align-items: center;">
            <strong>Terbilang :</strong>
            <div style="margin-left: 10px; padding: 2px 5px; background: repeating-linear-gradient(45deg, #f0f0f0, #f0f0f0 2px, transparent 2px, transparent 4px); width: 400px; height: 18px; border: 1px solid #ccc; display: flex; align-items: center;">
                <strong style="color: #000; font-size: 9px; line-height: 1;">{{ ucwords(trim($terbilang)) }} rupiah</strong>
            </div>
        </div>
        
        <!-- Totals Section -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Sub Total</td>
                    <td class="amount"><span class="currency-rp">Rp</span><span class="currency-amount">{{ number_format($subtotal, 0, ',', '.') }}</span></td>
                </tr>
                @if($includeTax)
                <tr>
                    <td class="label">PPN 11%</td>
                    <td class="amount"><span class="currency-rp">Rp</span><span class="currency-amount">{{ number_format($taxAmount, 0, ',', '.') }}</span></td>
                </tr>
                @endif
                <tr>
                    <td class="label">Total Faktur</td>
                    <td class="amount"><span class="currency-rp">Rp</span><span class="currency-amount">{{ number_format($finalAmount, 0, ',', '.') }}</span></td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer-section">
            <div class="footer-left" style="text-align: center;">
                <strong>Penerima</strong><br><br>
                <div class="signature-area">(....................................)</div>
            </div>
            <div class="footer-center">
                <strong>For Payment, Please Transfer to :</strong><br>
                <div class="bank-info">
                    <strong>{{ $bankInfo['account_name'] }}</strong><br>
                    <strong>{{ $bankInfo['name'] }}</strong><br>
                    <strong>NO. REK. {{ $bankInfo['account'] }}</strong>
                </div>
            </div>
            <div class="footer-right" style="text-align: center;">
                <strong>Hormat Kami,</strong><br><br>
                <div class="signature-area"><strong>(KADARUSMAN)</strong></div>
            </div>
        </div>
    </div>
    
    <script>
        // Page loaded - ready for manual print if needed
        window.onload = function() {
            // No auto print - user can manually print with Ctrl+P if needed
        }
    </script>
</body>
</html>
