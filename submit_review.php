<?php
include 'includes/db.php'; // DB connection
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id']; // VARCHAR
    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION['user_id']; // assuming session is set
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    // Check if user already reviewed this product
    $check = $conn->prepare("SELECT * FROM reviews WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        echo "You have already reviewed this product.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (order_id, product_id, user_id, rating, review, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("siiis", $order_id, $product_id, $user_id, $rating, $review);

        if ($stmt->execute()) {
            echo "Review submitted successfully!";
        } else {
            echo "Error submitting review.";
        }

        $stmt->close();
    }

    $check->close();
    $conn->close();
}
