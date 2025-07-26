<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $phone = $_POST['phone'];
    $line1 = $_POST['address_line_1'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $region = $_POST['region'];
    $zip = $_POST['zip_code'];
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    if ($is_default) {
        mysqli_query($conn, "UPDATE addresses SET is_default = 0 WHERE user_id = $user_id");
    }

    $stmt = $conn->prepare("INSERT INTO addresses (user_id, first_name, last_name, phone, address_line_1, barangay, city, province, region, zip_code, is_default)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssi", $user_id, $first, $last, $phone, $line1, $barangay, $city, $province, $region, $zip, $is_default);
    $stmt->execute();
    $stmt->close();

    header('Location: profile.php');
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h3 class="mb-3">Add New Address</h3>
    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="col-md-12">
            <label for="address_line_1" class="form-label">Address Line 1</label>
            <textarea name="address_line_1" class="form-control" required></textarea>
        </div>
        <div class="col-md-6">
            <label for="barangay" class="form-label">Barangay</label>
            <input type="text" name="barangay" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="city" class="form-label">City</label>
            <input type="text" name="city" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="province" class="form-label">Province</label>
            <input type="text" name="province" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="region" class="form-label">Region</label>
            <input type="text" name="region" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="zip_code" class="form-label">Zip Code</label>
            <input type="text" name="zip_code" class="form-control" required>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                <label class="form-check-label" for="is_default">
                    Set as Default Address
                </label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Save Address</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>