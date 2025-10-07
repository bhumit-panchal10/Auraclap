@php
    $grandTotal = 0;
    $total = 0;
@endphp

@foreach ($orderArr as $order)
    @foreach ($order['list'] as $item)
        @php
            $grandTotal += $item['amount'];
        @endphp
    @endforeach
@endforeach


@php
    $subTotal = $grandTotal;
    $discount = $order['iDiscount'] ?? 0;
    $total = $subTotal - $discount;
    $tax = $total * 0.18;
    $netAmount = $total + $tax;
    
    
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        /*@font-face {*/
        /*    font-family: 'DejaVu Sans';*/
        /*    src: url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');*/
        /*}*/
        /*body {*/
        /*    font-family: 'DejaVu Sans', sans-serif;*/
        /*    margin: 0;*/
        /*    padding: 20px;*/
        /*    color: #333;*/
        /*}*/
        .invoice-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        .invoice-header-image, .invoice-footer-image {
            width: 100%;
        }
        .invoice-header-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table thead {
            background-color: #f2f2f2;
            border: 1px solid #007bff;
        }
        .invoice-table th, .invoice-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .invoice-summary {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .invoice-totals {
            text-align: right;
        }
        .invoice-totals div:before {
            content: attr(data-label);
            display: inline-block;
            min-width: 100px;
        }
        .total-amount {
            font-weight: bold;
            font-size: 1.2em;
        }
        .authorised-sign {
            text-align: right;
            margin-top: 50px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <img src="https://admin.auraclap.com/assets/images/header.jpg" class="invoice-header-image">

    <div class="invoice-container">
        <div class="invoice-header-section">
            <div>
                <strong>Invoice to:</strong><br>
                {{ $Order->Customer_name ?? '' }}<br>
                {{ $Order->Customer_Address ?? '' }}<br>
                {{ $Order->Customer_phone ?? '' }}
            </div>
            <div class="invoice-details">
                <div>Invoice No: {{ $existingInvoice->invoice_id ?? 'N/A' }}</div>
                <div>Date: {{ date('d-m-Y', strtotime($existingInvoice->date ?? now())) }}</div>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Item Description</th>
                    <th>Qty</th>
                    <th>HSN</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderArr as $order)
                    @foreach ($order['list'] as $index => $item)
                      
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['strSubCategoryName'] ?? '' }}</td>
                            <td style="text-align: center">{{ $item['qty'] ?? 0 }}</td>
                            <td style="text-align: center">786543</td>
                            <td style="text-align: right">{{ number_format($item['rate'], 2) }}</td>
                            <td style="text-align: right">{{ number_format($item['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div class="invoice-summary">
            <div>
                <p>Thank you for your purchasing !!!</p>
                <p><strong>Address:</strong></p>
                <p>B-405, World trade tower, nr. Sanand Chokdi,</p>
                <p>S.G Highway, Makarba, Ahmedabad, 380000</p>
                <p>Email: auraclap@auraclap.com</p>
            </div>
            <div class="invoice-totals">
                <div data-label="Sub Total:"> {{ number_format($subTotal, 2) }}</div>
                <div data-label="Discount:"> {{ number_format($discount, 2) }}</div>
                <div data-label="Total:"> {{ number_format($total, 2) }}</div>
                <div data-label="Tax (18%):"> {{ number_format($tax, 2) }}</div>
                <div class="total-amount" data-label="Net Amount:"> {{ round($netAmount) }}</div>
            </div>
        </div>
    </div>

    <img src="https://admin.auraclap.com/assets/images/footer.jpg" class="invoice-footer-image">
</body>
</html>
