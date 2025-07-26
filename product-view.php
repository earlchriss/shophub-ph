<?php
include 'includes/db.php';
include 'includes/header.php';

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$product = null;
$images = [];

if ($product_id > 0) {
    // Fetch product info
    $sql = "SELECT * FROM products WHERE product_id = $product_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        // Fetch images
        $image_query = "SELECT image_path FROM product_images WHERE product_id = $product_id ORDER BY image_id ASC";
        $image_result = mysqli_query($conn, $image_query);
        while ($img_row = mysqli_fetch_assoc($image_result)) {
            $images[] = $img_row['image_path'];
        }
    }
}

?>

<div class="container mt-4">
    <?php if ($product): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="border rounded bg-white shadow-sm p-0">
                    <div class="p-3">
                        <div class="text-center mb-3">
                            <img id="mainImage"
                                src="admin/<?= htmlspecialchars($images[0]) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                style="max-width: 100%; max-height: 450px; object-fit: contain; border-radius: 6px;"
                                onerror="this.src='assets/img/placeholder.jpg';">
                        </div>

                        <div class="d-flex justify-content-center flex-wrap gap-2 pb-3">
                            <?php foreach ($images as $img): ?>
                                <div onclick="document.getElementById('mainImage').src='admin/<?= htmlspecialchars($img) ?>';"
                                    style="border: 2px solid #ccc; padding: 4px; cursor: pointer; border-radius: 5px;">
                                    <img src="admin/<?= htmlspecialchars($img) ?>"
                                        style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px;"
                                        onerror="this.src='assets/img/placeholder.jpg';">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>


                <!-- Description and Reviews Below Image -->
                <div class="mt-4 p-3 border rounded bg-light">
                    <h5>Description:</h5>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>

                <div class="mt-4 p-3 border rounded bg-white">
                    <h5>Reviews:</h5>
                    <p class="text-muted fst-italic">No reviews yet. Be the first to review this product.</p>
                </div>
            </div>

            <!-- Variants & Buy Options -->
            <div class="col-lg-6">
                <div class="product-container p-4">
                    <h1 class="h4 mb-3"><?= htmlspecialchars($product['name']) ?></h1>

                    <!-- Price -->
                    <div class="price-section mb-3">
                        <span class="fs-4 fw-bold text-danger">₱<?= number_format($product['price'], 2) ?></span>
                    </div>
                    <form action="cart_add.php" method="POST">
                        <?php
                        $variant_query = "SELECT variant_type, variant_value FROM product_variants WHERE product_id = $product_id";
                        $variant_result = $conn->query($variant_query);

                        $variants = [];

                        if ($variant_result && $variant_result->num_rows > 0) {
                            while ($row = $variant_result->fetch_assoc()) {
                                $type = strtolower($row['variant_type']);
                                $value = $row['variant_value'];
                                $variants[$type][] = $value;
                            }
                        }

                        ?>
                        <?php foreach ($variants as $type => $values): ?>
                            <div class="variant-selector mb-3">
                                <label for="variant_<?= $type ?>" class="form-label"><?= ucfirst($type) ?>:</label>
                                <select class="form-select" name="variant[<?= $type ?>]" id="variant_<?= $type ?>" required>
                                    <option value="">Select <?= $type ?></option>
                                    <?php foreach ($values as $value): ?>
                                        <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($value) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>


                        <div class="quantity-selector">
                            <span class="me-3">Quantity:</span>
                            <button class="quantity-btn" type="button" onclick="changeQuantity(-1)">-</button>
                            <input type="number" class="quantity-input" value="1" min="1" max="99" id="quantity" oninput="syncQuantity()">
                            <button class="quantity-btn" type="button" onclick="changeQuantity(1)">+</button>
                            <small class="text-muted ms-3"><?= $product['stock'] ?> pieces available</small>
                        </div>






                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <input type="hidden" name="name" value="<?= $product['name'] ?>">
                        <input type="hidden" name="price" value="<?= $product['price'] ?>">
                        <input type="hidden" name="qty" id="form_quantity" value="1">
                        <input type="hidden" name="image" value="<?= $images[0] ?? 'assets/img/placeholder.jpg' ?>">

                        <div class="action-buttons mt-4 d-flex gap-3">
                            <button type="submit" name="add_to_cart" class="btn btn-danger">
                                <i class="fas fa-cart-plus me-1"></i> Add to Cart
                            </button>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">Product not found.</div>
    <?php endif; ?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qtyInput = document.getElementById("quantity");
        const formInput = document.getElementById("form_quantity");

        window.changeQuantity = function(delta) {
            let value = parseInt(qtyInput.value) || 1;
            value = Math.max(1, Math.min(99, value + delta));
            qtyInput.value = value;
            formInput.value = value;
        };

        qtyInput.addEventListener("input", function() {
            const value = parseInt(this.value);
            formInput.value = isNaN(value) ? 1 : Math.max(1, Math.min(99, value));
        });
    });
</script>


<?php include 'includes/footer.php'; ?>