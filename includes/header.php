<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroShop - Electronics Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-orange: #ff5722;
            --secondary-orange: #ff7043;
            --light-orange: #ffccbc;
            --dark-orange: #d84315;
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-orange) !important;
        }

        .btn-primary {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
        }

        .btn-primary:hover {
            background-color: var(--dark-orange);
            border-color: var(--dark-orange);
        }

        .search-bar {
            border: 2px solid var(--primary-orange);
            border-radius: 25px;
        }

        .search-btn {
            background-color: var(--primary-orange);
            border: none;
            border-radius: 0 25px 25px 0;
        }

        .category-item {
            transition: transform 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .category-item:hover {
            transform: translateY(-5px);
            color: var(--primary-orange);
        }

        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
        }

        .price {
            color: var(--primary-orange);
            font-weight: bold;
            font-size: 1.2em;
        }

        .original-price {
            text-decoration: line-through;
            color: #666;
            font-size: 0.9em;
        }

        .discount-badge {
            background-color: var(--primary-orange);
            color: white;
            padding: 2px 8px;
            border-radius: 15px;
            font-size: 0.8em;
        }

        .rating {
            color: #ffc107;
        }

        .carousel-item img {
            height: 400px;
            object-fit: cover;
        }

        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 40px 0;
        }

        .footer h5 {
            color: var(--primary-orange);
            margin-bottom: 20px;
        }

        .footer a {
            color: #ecf0f1;
            text-decoration: none;
        }

        .footer a:hover {
            color: var(--primary-orange);
        }

        .flash-sale {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
        }

        .countdown {
            font-size: 1.5em;
            font-weight: bold;
        }

        .badge-new {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7em;
        }

        .social-icon {
            color: var(--primary-orange);
            font-size: 1.5em;
            margin: 0 10px;
            transition: transform 0.3s ease;
        }

        .social-icon:hover {
            transform: scale(1.2);
            color: var(--secondary-orange);
        }

        @media (max-width: 768px) {
            .carousel-item img {
                height: 250px;
            }

            .search-bar {
                margin: 10px 0;
            }
        }
    </style>
    <style>
        /* Spinner Wrapper */
        #loadingSpinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Spinner Animation */
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
    <style>
        :root {
            --primary-color: #ee4d2d;
            --secondary-color: #f53d2d;
            --accent-color: #ff6b35;
            --text-dark: #333;
            --text-light: #666;
            --bg-light: #f8f9fa;
            --border-color: #e0e0e0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            background-color: var(--bg-light);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .breadcrumb {
            background: transparent;
            padding: 0.5rem 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-item a {
            color: var(--text-light);
            text-decoration: none;
        }

        .product-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .product-image {
            position: relative;
            overflow: hidden;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }

        .main-image:hover {
            transform: scale(1.05);
        }

        .thumbnail-container {
            display: flex;
            gap: 10px;
            margin-top: 1rem;
            overflow-x: auto;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            border: 2px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            transition: border-color 0.3s ease;
            flex-shrink: 0;
        }

        .thumbnail:hover,
        .thumbnail.active {
            border-color: var(--primary-color);
        }

        .price-section {
            background: linear-gradient(135deg, #fff5f5, #ffe8e8);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .current-price {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .original-price {
            font-size: 1.1rem;
            color: var(--text-light);
            text-decoration: line-through;
        }

        .discount-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }

        .rating-section {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .stars {
            color: #ffc107;
        }

        .variant-selector {
            margin-bottom: 1.5rem;
        }

        .variant-option {
            border: 2px solid var(--border-color);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .variant-option:hover,
        .variant-option.active {
            border-color: var(--primary-color);
            background-color: #fff5f5;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            background: white;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background-color: var(--bg-light);
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            border: 1px solid var(--border-color);
            height: 40px;
            border-radius: 4px;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .btn-add-cart {
            background: linear-gradient(135deg, #ffa726, #ff9800);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn-add-cart:hover {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        }

        .btn-buy-now {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn-buy-now:hover {
            background: linear-gradient(135deg, var(--secondary-color), #d32f2f);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(238, 77, 45, 0.3);
        }



        .product-details {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .detail-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-label {
            width: 30%;
            color: var(--text-light);
            font-weight: 500;
        }

        .detail-value {
            width: 70%;
            color: var(--text-dark);
        }

        .shipping-info {
            background: #f8f9ff;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .feature-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            margin-right: 0.5rem;
        }

        @media (max-width: 768px) {
            .current-price {
                font-size: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .main-image {
                height: 300px;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-label,
            .detail-value {
                width: 100%;
            }
        }
    </style>
    <style>
        :root {
            --primary-color: #ee4d2d;
            --secondary-color: #f5f5f5;
            --text-color: #333;
            --border-color: #e5e5e5;
        }

        body {
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--primary-color);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white !important;
        }

        .checkout-header {
            background: white;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .product-item {
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .product-name {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .product-specs {
            color: #666;
            font-size: 0.9rem;
        }

        .price {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.1rem;
        }

        .original-price {
            color: #999;
            text-decoration: line-through;
            font-size: 0.9rem;
        }

        .address-section {
            background: #fff8f0;
            border-left: 3px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .shipping-option {
            border: 1px solid var(--border-color);
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .shipping-option:hover {
            border-color: var(--primary-color);
        }

        .shipping-option.selected {
            border-color: var(--primary-color);
            background-color: #fff8f0;
        }

        .payment-method {
            border: 1px solid var(--border-color);
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: var(--primary-color);
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: #fff8f0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
            border-top: 1px solid var(--border-color);
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #d63c1a;
            border-color: #d63c1a;
        }

        .voucher-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid var(--border-color);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid var(--border-color);
            height: 30px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }

            .card-body {
                padding: 1rem;
            }

            .product-image {
                width: 60px;
                height: 60px;
            }

            .row.g-4 {
                margin: 0;
            }

            .col-md-8,
            .col-md-4 {
                padding: 0;
            }
        }
    </style>
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