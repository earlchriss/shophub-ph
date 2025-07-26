<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Handle product removal
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    // Remove from both cart and variants
    mysqli_query($conn, "DELETE FROM cart WHERE id = $remove_id AND user_id = '$user_id'");
    header("Location: cart.php");
    exit;
}

// Fetch cart items
$cart_items = [];
$variant_map = [];

$sql = "SELECT * FROM cart WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
    }

    $cart_ids = array_column($cart_items, 'cart_id');

    if (!empty($cart_ids)) {
        $cart_ids_str = implode(',', array_map('intval', $cart_ids));

        $variant_sql = "SELECT * FROM cart_variants WHERE cart_id IN ($cart_ids_str)";
        $variant_result = mysqli_query($conn, $variant_sql);

        if ($variant_result) {
            while ($variant = mysqli_fetch_assoc($variant_result)) {
                $variant_map[$variant['cart_id']][] = $variant;
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4 fw-bold">🛒 Your Shopping Cart</h2>

    <?php if (!empty($cart_items)): ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <?php foreach ($cart_items as $item): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-3 text-center">
                                <img src="admin/<?= htmlspecialchars($item['product_image']) ?>" class="img-fluid rounded-start p-2" style="max-height: 120px;" onerror="this.src='assets/img/placeholder.jpg';">
                            </div>
                            <div class="col-md-9">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title mb-1"><?= htmlspecialchars($item['product_name']) ?></h5>

                                            <?php if (!empty($variant_map[$item['cart_id']])): ?>
                                                <p class="mb-1 text-muted">
                                                    <?php foreach ($variant_map[$item['cart_id']] as $variant): ?>
                                                        <span class="badge bg-dark me-1">
                                                            <?= htmlspecialchars(ucfirst($variant['variant_type'])) ?>: <?= htmlspecialchars($variant['variant_value']) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </p>
                                            <?php endif; ?>


                                            <p class="mb-1">Price: ₱<?= number_format($item['price'], 2) ?></p>
                                            <p class="mb-0">Quantity: <?= $item['quantity'] ?></p>
                                        </div>
                                        <form method="POST" action="cart_remove.php" class="d-inline">
                                            <input type="hidden" name="index" value="<?= $item['cart_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this item from cart?')">Remove</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Subtotal Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">🧾 Order Summary</h5>
                        <?php
                        $subtotal = 0;
                        foreach ($cart_items as $item) {
                            $subtotal += $item['price'] * $item['quantity'];
                        }
                        ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-semibold">Subtotal</span>
                                <span>₱<?= number_format($subtotal, 2) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Shipping Fee</span>
                                <span class="text-muted">₱0.00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between fw-bold fs-5">
                                <span>Total</span>
                                <span>₱<?= number_format($subtotal, 2) ?></span>
                            </li>
                        </ul>
                        <a href="checkout.php?from_cart=1" class="btn btn-warning w-100 mt-4">Proceed to Checkout</a>

                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Your cart is empty. 🛍️</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>