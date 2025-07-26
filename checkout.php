<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$address = [];
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $address = $result->fetch_assoc();
    $stmt->close();
}

$cart_products = [];
if (isset($_GET['from_cart']) && $_GET['from_cart'] == 1) {
    $query = "SELECT * FROM cart WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($item = mysqli_fetch_assoc($result)) {
            $cart_id = $item['cart_id'];
            $variants_result = mysqli_query($conn, "SELECT * FROM cart_variants WHERE cart_id = $cart_id");
            $variants = [];
            while ($variant = mysqli_fetch_assoc($variants_result)) {
                $variants[] = $variant;
            }
            $item['variants'] = $variants;
            $cart_products[] = $item;
        }
        $_SESSION['checkout_cart'] = $cart_products;
        mysqli_query($conn, "DELETE FROM cart_variants WHERE cart_id IN (SELECT cart_id FROM cart WHERE user_id = '$user_id')");
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");
        header("Location: checkout.php");
        exit;
    }
}

$cart_products = $_SESSION['checkout_cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    require 'includes/db.php';

    $user_id = $_SESSION['user_id'];
    $address_id = $_POST['address_id'] ?? null;
    $shipping_method = $_POST['shipping_method'] ?? 'Standard';
    $subtotal = floatval($_POST['subtotal'] ?? 0);
    $shipping_fee = floatval($_POST['shipping_fee'] ?? 0);
    $promo_discount = floatval($_POST['voucher'] ?? 0);
    $total = floatval($_POST['total'] ?? 0);
    $promo_code = $_POST['promo_code'] ?? '';

    $random_number = rand(10000, 99999);
    $order_uid = 'ES' . $random_number;

    $stmt = $conn->prepare("INSERT INTO orders 
        (order_id, user_id, address_id, shipping_method, subtotal, shipping_fee, promo_code, promo_discount, total, order_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Pending')");
    $stmt->bind_param(
        "siissddds",
        $order_uid,
        $user_id,
        $address_id,
        $shipping_method,
        $subtotal,
        $shipping_fee,
        $promo_code,
        $promo_discount,
        $total
    );
    $stmt->execute();
    $stmt->close();

    if (!empty($_SESSION['checkout_cart'])) {
        foreach ($_SESSION['checkout_cart'] as $item) {
            $product_id = $item['product_id'];
            $product_name = $item['product_name'];
            $product_image = $item['product_image'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $subtotal_item = $quantity * $price;

            $stmt = $conn->prepare("INSERT INTO order_items 
                (order_id, product_id, product_name, product_image, quantity, price, subtotal) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "sissidd",
                $order_uid,
                $product_id,
                $product_name,
                $product_image,
                $quantity,
                $price,
                $subtotal_item
            );
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE products 
                SET stock = stock - ?, sold = sold + ? 
                WHERE product_id = ?");
            $stmt->bind_param("iii", $quantity, $quantity, $product_id);
            $stmt->execute();
            $stmt->close();

            if (!empty($item['variants'])) {
                foreach ($item['variants'] as $variant) {
                    $variant_type = $variant['variant_type'];
                    $variant_value = $variant['variant_value'];

                    $stmt = $conn->prepare("INSERT INTO order_item_variants 
                        (order_id, product_id, variant_type, variant_value) 
                        VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("siss", $order_uid, $product_id, $variant_type, $variant_value);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            if (!empty($promo_code)) {
                $stmt = $conn->prepare("UPDATE vouchers SET is_used = 1 WHERE code = ?");
                $stmt->bind_param("s", $promo_code);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    unset($_SESSION['checkout_cart']);
    header("Location: order_success.php?order_id=$order_uid");
    exit();
}



include 'includes/header.php';
?>

<div class="container my-4">
    <div class="row g-4">
        <!-- Left pColumn - Main Checkout Form -->
        <div class="col-md-8">
            <!-- Delivery Address -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        Delivery Address
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($address)): ?>
                        <div class="address-section">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($address['first_name'] . ' ' . $address['last_name']) ?> (<?= htmlspecialchars($address['phone']) ?>)</h6>
                                    <p class="mb-1"><?= htmlspecialchars($address['address_line_1']) ?></p>
                                    <p class="mb-0">
                                        <?= htmlspecialchars($address['city']) ?>,
                                        <?= htmlspecialchars($address['province']) ?>,
                                        <?= htmlspecialchars($address['region']) ?> <?= htmlspecialchars($address['zip_code']) ?>
                                    </p>
                                    <?php if ($address['is_default']): ?>
                                        <span class="badge bg-primary">Default</span>
                                    <?php endif; ?>
                                </div>
                                <a href="profile.php#addresses" class="btn btn-outline-primary btn-sm">Change</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>No delivery address found. <a href="profile.php#addresses" class="btn btn-sm btn-primary">Add Address</a></p>
                    <?php endif; ?>
                </div>
            </div>



            <!-- Products Ordered -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-box text-primary me-2"></i>
                        Products Ordered
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($cart_products)): ?>
                        <?php foreach ($cart_products as $item): ?>
                            <div class="product-item">
                                <div class="row align-items-center">
                                    <div class="col-md-2 col-3">
                                        <img src="admin/<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="product-image">
                                    </div>
                                    <div class="col-md-6 col-9">
                                        <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                        <div class="product-specs">
                                            <?php foreach ($item['variants'] as $variant): ?>
                                                <span class="badge bg-secondary me-1"><?= htmlspecialchars(ucfirst($variant['variant_type'])) ?>: <?= htmlspecialchars($variant['variant_value']) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6">
                                        <div class="quantity-control">
                                            <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 text-end">
                                        <div class="price">₱<?= number_format($item['price'], 2) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No product selected for checkout.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $region = $address['region'] ?? '';
            $city = $address['city'] ?? '';
            $province = $address['province'] ?? '';

            // Example condition: Only Metro Manila cities can use Same Day Delivery
            $is_same_day_available = in_array(strtolower($city), [
                'manila',
                'quezon city',
                'makati',
                'taguig',
                'pasay',
                'mandaluyong',
                'pasig',
                'caloocan',
                'san juan',
                'marikina',
                'parañaque',
                'las piñas',
                'malabon',
                'navotas',
                'valenzuela'
            ]);

            // Example condition: Only Luzon region can use Express
            $is_express_available = stripos($region, 'Luzon') !== false;
            ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-truck text-primary me-2"></i>
                        Shipping Options
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Standard Delivery - Always available -->
                    <!-- Standard -->
                    <div class="shipping-option selected" onclick="selectShipping(this)" data-fee="50">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <input type="radio" name="shipping" value="standard" checked>
                                <strong class="ms-2">Standard Delivery</strong>
                                <div class="ms-4 text-muted">Receive by Jul 8 - Jul 10</div>
                            </div>
                            <div class="price">₱50</div>
                        </div>
                    </div>

                    <!-- Express Delivery -->
                    <?php if ($is_express_available): ?>
                        <!-- Express -->
                        <div class="shipping-option" onclick="selectShipping(this)" data-fee="100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="radio" name="shipping" value="express">
                                    <strong class="ms-2">Express Delivery</strong>
                                    <div class="ms-4 text-muted">Receive by Jul 7</div>
                                </div>
                                <div class="price">₱100</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Same Day Delivery -->
                    <?php if ($is_same_day_available): ?>
                        <!-- Same Day -->
                        <div class="shipping-option" onclick="selectShipping(this)" data-fee="200">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="radio" name="shipping" value="same-day">
                                    <strong class="ms-2">Same Day Delivery</strong>
                                    <div class="ms-4 text-muted">Receive today before 9 PM</div>
                                </div>
                                <div class="price">₱200</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!$is_express_available && !$is_same_day_available): ?>
                <div class="alert alert-info mt-2">Only Standard Delivery is available for your area.</div>
            <?php endif; ?>


            <!-- Payment Method -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        Payment Method
                    </h5>
                </div>
                <div class="card-body">
                    <div class="payment-method selected" onclick="selectPayment(this)">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="payment" value="gcash" checked>
                            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=40&h=40&fit=crop"
                                alt="GCash" class="ms-2 me-3" style="width: 40px; height: 40px;">
                            <div>
                                <strong>GCash</strong>
                                <div class="text-muted">Pay with GCash e-wallet</div>
                            </div>
                        </div>
                    </div>
                    <div class="payment-method" onclick="selectPayment(this)">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="payment" value="card">
                            <i class="fas fa-credit-card ms-2 me-3 text-primary" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>Credit/Debit Card</strong>
                                <div class="text-muted">Visa, Mastercard, American Express</div>
                            </div>
                        </div>
                    </div>
                    <div class="payment-method" onclick="selectPayment(this)">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="payment" value="cod">
                            <i class="fas fa-money-bill-wave ms-2 me-3 text-success" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>Cash on Delivery</strong>
                                <div class="text-muted">Pay when you receive your order</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $subtotal = 0;
        $shipping_fee = 50; // You can change this if dynamic
        $promo_code = $_POST['promo_code'] ?? '';
        $promo_discount = 0;

        foreach ($cart_products as $item) {
            $price = floatval($item['price']);
            $qty = intval($item['quantity']);
            $subtotal += ($price * $qty);
        }

        if (isset($_POST['apply_voucher']) && !empty($promo_code)) {
            $stmt = $conn->prepare("SELECT discount, expiry, is_used FROM vouchers WHERE code = ?");
            $stmt->bind_param("s", $promo_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $current_date = date('Y-m-d');

                if ($row['is_used']) {
                    $voucher_error = "This voucher has already been used.";
                    $promo_code = '';
                } elseif ($row['expiry'] < $current_date) {
                    $voucher_error = "This voucher has expired.";
                    $promo_code = '';
                } else {
                    $promo_discount = floatval($row['discount']);
                }
            } else {
                $voucher_error = "Invalid voucher code.";
                $promo_code = '';
            }
            $stmt->close();
        }


        $total = ($subtotal + $shipping_fee) - $promo_discount;



        ?>

        <!-- Right Column - Order Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Voucher Section -->
                    <form method="POST" class="voucher-section d-flex gap-2 mb-3">
                        <input type="text" name="promo_code" class="form-control" placeholder="Enter voucher code" required>
                        <button type="submit" name="apply_voucher" class="btn btn-outline-primary">Apply</button>
                    </form>
                    <?php if (isset($voucher_error)): ?>
                        <div class="alert alert-danger mt-2"><?= $voucher_error ?></div>
                    <?php elseif ($promo_discount > 0): ?>
                        <div class="alert alert-success mt-2">
                            Voucher applied: ₱<?= number_format($promo_discount, 2) ?> off
                        </div>
                    <?php endif; ?>



                    <div class="order-summary">
                        <h4>Order Summary</h4>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>₱<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <!-- Update span IDs for JS -->
                        <div class="summary-row">
                            <span>Shipping Fee:</span>
                            <span id="shipping-fee-display">₱<?= number_format($shipping_fee, 2) ?></span>
                        </div>

                        <div class="summary-row">
                            <span>Promo Discount:</span>
                            <span class="text-success">-₱<?= number_format($promo_discount, 2) ?></span>
                        </div>
                        <hr>
                        <div class="summary-row total">
                            <strong>Total:</strong>
                            <strong id="total-display">₱<?= number_format($total, 2) ?></strong>
                        </div>
                        <!-- Update hidden input IDs -->
                        <input type="hidden" id="shipping_fee" name="shipping_fee" value="<?= $shipping_fee ?>">
                        <input type="hidden" id="total" name="total" value="<?= $total ?>">

                        <!-- Hidden inputs to submit totals -->
                        <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                        <input type="hidden" name="shipping_fee" value="<?= $shipping_fee ?>">
                        <input type="hidden" name="voucher" value="<?= $promo_discount ?>">
                        <input type="hidden" name="total" value="<?= $total ?>">
                        <input type="hidden" name="promo_code" value="<?= htmlspecialchars($promo_code) ?>">
                    </div>



                    <form id="orderForm" method="POST" action="checkout.php">
                        <!-- Add this hidden address field -->
                        <input type="hidden" name="address_id" value="<?= htmlspecialchars($address['address_id']) ?>">
                        <!-- Hidden inputs to submit totals -->
                        <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                        <input type="hidden" name="shipping_fee" value="<?= $shipping_fee ?>">
                        <input type="hidden" name="voucher" value="<?= $voucher ?>">
                        <input type="hidden" name="total" value="<?= $total ?>">

                        <button type="submit" name="place_order" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-lock me-2"></i>Place Order
                        </button>
                    </form>


                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Your payment information is secure
                        </small>
                    </div>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <h6 class="mb-3">Why Choose ElectroShop?</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="fas fa-shipping-fast text-primary mb-2" style="font-size: 1.5rem;"></i>
                            <small>Fast Delivery</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-undo-alt text-success mb-2" style="font-size: 1.5rem;"></i>
                            <small>Easy Returns</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-headset text-info mb-2" style="font-size: 1.5rem;"></i>
                            <small>24/7 Support</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelector(".btn-outline-primary").addEventListener("click", function() {
        const voucherInput = document.querySelector('input[placeholder="Enter voucher code"]').value;
        let voucherDiscount = 0;

        // Example fixed promo code logic
        if (voucherInput.toLowerCase() === "save50") {
            voucherDiscount = 50;
        }

        // Update DOM
        document.querySelector(".text-success").textContent = "-₱" + voucherDiscount.toFixed(2);

        // Update total display
        const subtotal = <?= $subtotal ?>;
        const shipping = <?= $shipping_fee ?>;
        const total = subtotal + shipping - voucherDiscount;

        document.getElementById("total-amount").textContent = "₱" + total.toFixed(2);
        document.getElementById("final-total").textContent = "₱" + total.toFixed(2);
    });
</script>
<script>
    function formatCurrency(amount) {
        return '₱' + parseFloat(amount).toFixed(2);
    }

    function selectShipping(option) {
        // Remove 'selected' class from all
        document.querySelectorAll('.shipping-option').forEach(opt => opt.classList.remove('selected'));

        // Add 'selected' to clicked
        option.classList.add('selected');

        // Set radio button as checked
        const radio = option.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;

        // Get shipping fee
        const fee = parseFloat(option.getAttribute('data-fee')) || 0;

        // Get current subtotal and promo
        const subtotal = parseFloat(<?= $subtotal ?>);
        const promo = parseFloat(<?= $promo_discount ?>);

        // Calculate new total
        const total = subtotal + fee - promo;

        // Update UI
        document.getElementById('shipping-fee-display').textContent = formatCurrency(fee);
        document.getElementById('total-display').textContent = formatCurrency(total);

        // Update hidden inputs
        document.getElementById('shipping_fee').value = fee;
        document.getElementById('total').value = total;
    }
</script>

<?php include 'includes/footer.php' ?>