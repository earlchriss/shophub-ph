<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }

        body {
            font-size: 14px;
        }

        .receipt-box {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px dashed #000;
        }

        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="receipt-box">
        <div class="receipt-title">Shopee Order Receipt</div>

        <p><strong>Order ID:</strong> ES12345</p>
        <p><strong>Date:</strong> July 15, 2025</p>

        <hr>

        <p><strong>Customer Name:</strong> John Doe</p>
        <p><strong>Contact Number:</strong> (+63) 912-345-6789</p>
        <p><strong>Delivery Address:</strong> 123 Electronics Street, Tech District, Cebu City, Central Visayas 6000</p>

        <hr>

        <h6>Order Details:</h6>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Variant</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Wireless Earbuds</td>
                    <td>1</td>
                    <td>Color: Black</td>
                    <td>₱1,299.00</td>
                    <td>₱1,299.00</td>
                </tr>
                <tr>
                    <td>USB Charger</td>
                    <td>2</td>
                    <td>Type: C</td>
                    <td>₱299.00</td>
                    <td>₱598.00</td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            <table class="table table-borderless table-sm w-auto">
                <tr>
                    <th>Subtotal:</th>
                    <td>₱1,897.00</td>
                </tr>
                <tr>
                    <th>Shipping:</th>
                    <td>₱50.00</td>
                </tr>
                <tr>
                    <th>Total:</th>
                    <td><strong>₱1,947.00</strong></td>
                </tr>
            </table>
        </div>

        <hr>

        <p><strong>Payment Method:</strong> Cash on Delivery</p>
        <p><strong>Status:</strong> Pending</p>
    </div>

    <div class="text-center mt-3 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-sm">Print Receipt</button>
    </div>
</body>

</html>