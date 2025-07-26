<?php
require 'db.php';
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order info
    $sql = "SELECT o.*, u.username, u.email, a.phone, a.address_line_1, a.barangay, a.city, a.province, a.region
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN addresses a ON o.address_id = a.address_id
            WHERE o.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Fetch ordered products
    $products = [];
    $sql = "SELECT oi.product_name, oi.quantity, oi.price
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();

    $sender_name = "ElectroHub Warehouse";
    $sender_city = "Mandaluyong City";
    $sender_barangay = "Addition Hills";
    $sender_province = "Metro Manila";
    $sender_zip = "1550";
    $branch = "ElectroHub Main Branch";
    $cashier = "John Dela Cruz";

?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Delivery Receipt</title>
        <style>
            @media print {
                body {
                    width: 80mm;
                }
            }

            body {
                font-family: Arial, sans-serif;
                font-size: 10px;
                width: 80mm;
                margin: 0 auto;
                padding: 10px;
            }

            .logo {
                text-align: center;
            }

            .logo img {
                max-width: 60px;
                height: auto;
            }

            .center {
                text-align: center;
            }

            .section {
                margin-bottom: 5px;
                border-bottom: 1px dashed #333;
                padding-bottom: 5px;
            }

            .label-title {
                font-weight: bold;
            }

            .boxed {
                border: 1px solid #000;
                padding: 4px;
                margin-top: 4px;
                text-align: center;
                font-size: 8px;
            }

            .footer {
                margin-top: 10px;
                font-size: 8px;
                text-align: center;
            }

            table {
                width: 100%;
                font-size: 10px;
                margin-top: 5px;
                border-collapse: collapse;
            }

            th,
            td {
                text-align: left;
                padding: 2px 0;
            }

            .total {
                font-weight: bold;
                text-align: right;
            }

            table,
            th,
            td {
                border: 1px dashed black;
            }

            th,
            td {
                padding: 3px;
                text-align: left;
            }
        </style>
    </head>

    <body onload="window.print()">
        <div class="logo">
            <img src="logo.jpg" alt="ElectroHub Logo">
            <div><strong>ElectroHub</strong></div>
            <div>Order Delivery Receipt</div>
        </div>

        <div class="section">
            <div class="label-title">Order ID:</div>
            <div><?= htmlspecialchars($order_id) ?></div>
        </div>

        <div class="section">
            <div class="label-title">BUYER</div>
            <div><?= htmlspecialchars($order['username']) ?></div>
            <div><?= htmlspecialchars($order['address_line_1']) ?>, <?= htmlspecialchars($order['barangay']) ?></div>
            <div><?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['province']) ?></div>
            <div><?= htmlspecialchars($order['region']) ?> | <?= htmlspecialchars($order['phone']) ?></div>
        </div>

        <div class="section">
            <div class="label-title">SELLER</div>
            <div><?= $sender_name ?></div>
            <div><?= $sender_barangay ?>, <?= $sender_city ?></div>
            <div><?= $sender_province ?> <?= $sender_zip ?></div>
        </div>

        <div class="section">
            <div class="label-title">Branch:</div>
            <div><?= $branch ?></div>
            <div class="label-title">Cashier:</div>
            <div><?= $cashier ?></div>
        </div>

        <div class="section">
            <div class="label-title">ORDERED PRODUCTS</div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="total">Total:</td>
                        <td class="total">₱<?= number_format($order['total'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>


        <!-- <div class="section center">
            <div><strong>Weight:</strong> KG</div>
        </div> -->

        <div class="boxed">
            Return to sender if undelivered after 7 days
        </div>

        <div class="footer">
            Thank you for shopping with ElectroHub!
        </div>
    </body>

    </html>
<?php } ?>