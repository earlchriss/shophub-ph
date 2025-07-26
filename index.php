<?php

session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroShop - Electronics Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="./"><i class="fas fa-bolt me-2"></i>ElectroShop</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="mx-auto col-lg-6">
                    <div class="input-group">
                        <input type="text" class="form-control search-bar" placeholder="Search for electronics...">
                        <button class="btn btn-primary search-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart (0)</a>
                    </li>
                    <?php
                    if (isset($_SESSION['auth'])) {
                    ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                <li><a class="dropdown-item" href="my_purchases.php">My Purchases</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php
                    } else {
                    ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-user"></i> Login</a>
                        </li>
                    <?php
                    }
                    ?>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Carousel -->
    <div class="container mt-4">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            </div>

            <div class="carousel-inner rounded-3">
                <div class="carousel-item active">
                    <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=1200&h=400&fit=crop" class="d-block w-100" alt="Electronics Sale">
                    <div class="carousel-caption">
                        <h3>Mega Electronics Sale</h3>
                        <p>Up to 70% off on latest gadgets</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?w=1200&h=400&fit=crop" class="d-block w-100" alt="Smartphone Deals">
                    <div class="carousel-caption">
                        <h3>Latest Smartphones</h3>
                        <p>Discover the newest technology</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1593305841991-05c297ba4575?w=1200&h=400&fit=crop" class="d-block w-100" alt="Gaming Setup">
                    <div class="carousel-caption">
                        <h3>Gaming Paradise</h3>
                        <p>Build your ultimate gaming setup</p>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>

    <!-- Flash Sale -->
    <div class="container mt-5" id="flashSaleContainer">
        <div class="flash-sale">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3><i class="fas fa-bolt me-2"></i>Flash Sale</h3>
                    <p class="mb-0">Limited time offers ending soon!</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="countdown">
                        <span id="hours">00</span>:
                        <span id="minutes">00</span>:
                        <span id="seconds">00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container mt-5">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row">
            <?php
            $conn = new mysqli("localhost", "root", "", "shophub");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "
        SELECT 
    p.product_id,
    p.name,
    p.price,
    p.sold,
    p.created_at,
    img.image_path
FROM products p
LEFT JOIN (
    SELECT product_id, image_path
    FROM product_images
    WHERE is_primary = 1
) img ON p.product_id = img.product_id
WHERE p.status = 'active'
ORDER BY p.created_at DESC
LIMIT 12

        ";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $name = htmlspecialchars($row['name']);
                    $price = floatval($row['price']);
                    // $discount = floatval($row['discount_percent']);
                    $sold = intval($row['sold']);
                    $image = !empty($row['image_path']) ? 'admin/' . $row['image_path'] : 'assets/img/placeholder.jpg';
                    // $final_price = $price - ($price * $discount / 100);
            ?>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card product-card h-100 shadow-sm">
                            <a href="product-view.php?product_id=<?= $row['product_id'] ?>" class="stretched-link"></a>
                            <div class="position-relative">
                                <img
                                    src="<?= htmlspecialchars($image) ?>"
                                    class="card-img-top product-image"
                                    alt="<?= $name ?>"
                                    loading="lazy"
                                    onerror="this.src='assets/img/placeholder.jpg';" />
                                <!-- -->
                                <?php
                                $created_at = new DateTime($row['created_at']);
                                $now = new DateTime();
                                $interval = $now->diff($created_at);
                                $isNew = ($interval->days < 2); // less than 2 full days
                                ?>
                                <?php if ($isNew): ?>
                                    <span class="badge-new position-absolute top-0 start-0 m-1 bg-success text-white px-2 py-1 rounded small">NEW</span>
                                <?php endif; ?>

                            </div>
                            <div class="card-body text-center p-2">
                                <h6 class="card-title mb-1" style="font-size: 0.95rem;"><?= $name ?></h6>
                                <div class="rating mb-1" style="font-size: 0.85rem;">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php endfor; ?>
                                    <span class="text-muted ms-1">(4.8)</span>
                                </div>
                                <div class="price fw-bold text-danger mb-1" style="font-size: 1rem;">₱<?= number_format($price, 2) ?></div>
                                <!-- <div class="original-price text-muted mb-1" style="font-size: 0.85rem;"><del>₱</del></div> -->
                                <small class="text-muted">Sold: <?= number_format($sold) ?></small>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="text-center text-muted">No active products found.</p>';
            }

            $conn->close();
            ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>