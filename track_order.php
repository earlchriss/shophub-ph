<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? '';
if (empty($order_id)) {
    echo "Invalid order.";
    exit;
}

// Fetch order tracking history
$stmt = $conn->prepare("SELECT status, remarks, created_at FROM order_tracking WHERE order_id = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$tracking = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Track Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .timeline {
            position: relative;
            padding-left: 39px;
            margin-top: 29px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 18.5px;
            width: 2px;
            height: 100%;
            background: #0d6efd;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 19px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            top: 1;
            left: -25px;
            width: 12px;
            height: 12px;
            background: #0d6efd;
            border-radius: 50%;
            z-index: 1;
        }
    </style>

</head>

<body>
    <div class="container py-5">
        <h3 class="mb-4">Track Order #<?= htmlspecialchars($order_id) ?></h3>

        <?php if (count($tracking) === 0): ?>
            <div class="alert alert-info">No tracking information available yet.</div>
        <?php else: ?>
            <div id="tracking-container">
                <!-- Tracking data will load here via AJAX -->
            </div>
        <?php endif; ?>


        <a href="my_purchases.php" class="btn btn-secondary mt-4">Back to My Purchases</a>
    </div>

    <script>
        function fetchTracking() {
            const orderId = "<?= htmlspecialchars($order_id) ?>";
            fetch(`fetch_tracking.php?order_id=${orderId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('tracking-container').innerHTML = data;
                });
        }

        // Initial fetch
        fetchTracking();

        // Refresh every 5 seconds
        setInterval(fetchTracking, 5000);
    </script>

</body>

</html>