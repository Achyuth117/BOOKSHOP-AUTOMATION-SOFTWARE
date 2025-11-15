<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['customer'];

$customer = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM customers WHERE email='$email'"
));

if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    mysqli_query(
        $conn,
        "UPDATE customers 
         SET name='$name', phone='$phone', address='$address' 
         WHERE email='$email'"
    );

    $msg = "Profile updated successfully!";
    $customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'"));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        body { background: #f4f6f9; }
        .profile-card {
            max-width: 600px;
            margin: 40px auto;
            padding: 25px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">📚 Smart Book Shop</span>
        <div>
            <a href="home.php" class="btn btn-outline-light">Home</a>
        </div>
    </div>
</nav>

<div class="profile-card">
    <h3 class="text-center mb-3">👤 My Profile</h3>

    <?php if (isset($msg)): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= $customer['name'] ?>" class="form-control mb-3" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= $customer['phone'] ?>" class="form-control mb-3">

        <label>Address</label>
        <textarea name="address" class="form-control mb-3"><?= $customer['address'] ?></textarea>

        <button class="btn btn-primary w-100" name="update_profile">Update Profile</button>
    </form>
</div>

</body>
</html>

