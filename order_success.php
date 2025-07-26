<?php
$order_id = $_GET['order_id'] ?? 0;
?>
<!DOCTYPE html>
<html>

<head>
    <title>Order Placed</title>
</head>

<body>
    <h1>✅ Thank you for your order!</h1>
    <p>Your Order ID: <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
    <a href="index.php">Back to Home</a>
</body>

</html>