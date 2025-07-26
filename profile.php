<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_result);

$address_result = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id = $user_id");
$addresses = mysqli_fetch_all($address_result, MYSQLI_ASSOC);

include 'includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-3">My Profile</h2>
    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
        </div>
    </div>

    <h4 class="mb-3">My Addresses</h4>
    <a href="add_address.php" class="btn btn-primary mb-3">Add New Address</a>

    <?php foreach ($addresses as $address): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title"><?= htmlspecialchars($address['first_name'] . ' ' . $address['last_name']) ?></h6>
                <p class="mb-1"><?= htmlspecialchars($address['phone']) ?></p>
                <p class="mb-1">
                    <?= htmlspecialchars($address['address_line_1']) ?>, <?= htmlspecialchars($address['barangay']) ?>, <?= htmlspecialchars($address['city']) ?>,<br>
                    <?= htmlspecialchars($address['province']) ?>, <?= htmlspecialchars($address['region']) ?> <?= htmlspecialchars($address['zip_code']) ?>
                </p>

                <div class="mt-2">
                    <?php if ($address['is_default']): ?>
                        <span class="badge bg-success">Default</span>
                    <?php else: ?>
                        <a href="set_default_address.php?id=<?= $address['address_id'] ?>" class="btn btn-sm btn-outline-primary">Set as Default</a>
                    <?php endif; ?>
                    <a href="delete_address.php?id=<?= $address['address_id'] ?>" class="btn btn-sm btn-outline-danger">Delete</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>