<?php
require '../includes/db.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Order info with full address
    $stmt = $conn->prepare("SELECT o.*, u.username, u.email, a.first_name, a.last_name,
                               a.address_line_1, a.city, a.province, a.region, a.zip_code , a.phone
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        LEFT JOIN addresses a ON o.address_id = a.address_id 
                        WHERE o.order_id = ?");


    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if (!$order) {
        echo "<div class='alert alert-danger'>Order not found.</div>";
        exit;
    }

    // Fetch order items
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $items = $stmt->get_result();
    $stmt->close();

    ob_start();
?>

    <h5>Order #: <?= htmlspecialchars($order['order_id']) ?></h5>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order['username']) ?> (<?= htmlspecialchars($order['email']) ?>)</p>
    <p><strong>Address:</strong>
        <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?>, <?= htmlspecialchars($order['phone']) ?><br>
        <?= htmlspecialchars($order['address_line_1']) ?>, <?= htmlspecialchars($order['city']) ?>,
        <?= htmlspecialchars($order['province']) ?>, <?= htmlspecialchars($order['region']) ?>
    </p>
    <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>

    <hr>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><img src="<?= htmlspecialchars($item['product_image']) ?>" width="50" height="50"></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱<?= number_format($item['price'], 2) ?></td>
                    <td>₱<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <form id="updateOrderForm">
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
        <div class="mb-3">
            <label>Status</label>
            <select class="form-select" name="status">
                <option <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option <?= $order['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                <option <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                <option <?= $order['status'] == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                <option <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                <option <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Remarks</label>
            <textarea class="form-control" name="remarks" rows="3"><?= htmlspecialchars($order['remarks']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Update Order</button>
    </form>

<?php
    echo ob_get_clean();
}
?>