<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $deliveryNumber ?? 'SJ/1036/IX/MSA/25' }}</title>
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
        
        .delivery-container {
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
        
        .company-info {
            flex: 1;
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
            width: 200px;
        }
        
        .date-info {
            margin-bottom: 5px;
        }
        
        .customer-info {
            text-align: right;
        }
        
        .customer-info strong {
            font-weight: bold;
        }
        
        .delivery-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            padding: 4px 0;
            margin: 4px 0;
        }
        
        .delivery-details {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            font-size: 10px;
            border: 1px solid #000;
            margin-bottom: 0;
        }
        
        .delivery-left {
            width: 50%;
        }
        
        .delivery-right {
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
            padding-left: 8px;
        }
        
        .products-table .text-center {
            text-align: center;
        }
        
        .footer-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            align-items: flex-end;
        }
        
        .footer-left, .footer-center, .footer-right {
            width: 30%;
            text-align: center;
            height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .signature-area {
            min-height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-bottom: 2px;
            margin-top: auto;
        }
        
        .signature-name {
            font-weight: bold;
        }
        
        @media print {
            body { 
                margin: 0; 
                padding: 0;
                background-color: white !important;
            }
            .delivery-container { 
                border: none; 
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="delivery-container">
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
                <div class="date-info">
                    Bogor, {{ now()->format('d F Y') }}<br>
                    Kepada Yth,<br>
                    <strong>{{ $customerName ?? 'Klinik Pratama Zam Zam Medika' }}</strong>
                </div>
                <div class="customer-info">
                    @if($customer && $customer->address)
                        {!! nl2br(e($customer->address)) !!}
                    @else
                        Jl. Raya Dramaga RT.02/RW.02<br>
                        Leuwikopo, Kec. Dramaga, Kota Bogor<br>
                        Jawa Barat
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Delivery Title -->
        <div class="delivery-title">SURAT JALAN</div>
        
        <!-- Delivery Details -->
        <div class="delivery-details">
            <div class="delivery-left">
                <div class="detail-row"><strong>No. Surat Jalan : {{ $deliveryNumber ?? 'SJ/1015/IX/MSA/25' }}</strong></div>
                <div class="detail-row" style="margin-top: 5px;"><strong>Telah kami terima dengan baik barang sebagai berikut :</strong></div>
            </div>
            <div class="delivery-right">
                <div class="detail-row">&nbsp;</div>
            </div>
        </div>
    
    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 200px;">Description</th>
                <th style="width: 80px;">ED</th>
                <th style="width: 80px;">Cat No.</th>
                <th style="width: 80px;">Lot No.</th>
                <th style="width: 50px;">QTY</th>
                <th style="width: 100px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cartData as $index => $item)
                @php
                    $product = \App\Models\Product::find($item['product_id']);
                    $unit = $product ? $product->unit : 'pcs';
                @endphp
                <tr>
                    <td class="center-cell">{{ $index + 1 }}</td>
                    <td class="text-left">{{ $product ? $product->name : 'Unknown Product' }}</td>
                    <td class="center-cell">{{ $product && $product->expired_date ? \Carbon\Carbon::parse($product->expired_date)->format('d/m/Y') : '' }}</td>
                    <td class="center-cell">{{ $product ? $product->code : '' }}</td>
                    <td class="center-cell">{{ $product ? $product->lot_number : '' }}</td>
                    <td class="center-cell">{{ $item['quantity'] }} {{ $unit }}</td>
                    <td class="center-cell"></td>
                </tr>
            @endforeach
            
            <!-- Empty rows to fill space -->
            @for($i = count($cartData); $i < 6; $i++)
                <tr>
                    <td class="center-cell">&nbsp;</td>
                    <td class="text-left">&nbsp;</td>
                    <td class="center-cell">&nbsp;</td>
                    <td class="center-cell">&nbsp;</td>
                    <td class="center-cell">&nbsp;</td>
                    <td class="center-cell">&nbsp;</td>
                    <td class="center-cell">&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>
    
    <!-- Footer -->
    <div class="footer-section">
        <div class="footer-left">
            <div style="height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: flex-start;">
                <div style="height: 10px;"></div>
                <strong>Penerima</strong>
                <div style="height: 30px;"></div>
            </div>
            <div class="signature-area">
                <span class="signature-name">(....................................)</span>
            </div>
        </div>
        <div class="footer-center">
            <div style="height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: flex-start;">
                <strong>Hormat Kami,</strong>
                <strong>Penanggung Jawab</strong>
                <strong>Teknis</strong>
            </div>
            <div class="signature-area">
                <span class="signature-name">(Ria Kamelia)</span>
            </div>
        </div>
        <div class="footer-right">
            <div style="height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: flex-start;">
                <strong>Hormat Kami,</strong>
                <strong>Pengirim</strong>
                <div style="height: 20px;"></div>
            </div>
            <div class="signature-area">
                <span class="signature-name">(Yayuk P. Wardani)</span>
            </div>
        </div>
    </div>
    </div>
</body>
</html>
