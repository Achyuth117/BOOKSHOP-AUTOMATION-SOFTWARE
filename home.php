<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['customer'];
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'"));
$customer_id = $customer['id'];

// Count total orders
$order_count = mysqli_fetch_row(mysqli_query(
    $conn,
    "SELECT COUNT(*) FROM orders WHERE customer_id=$customer_id"
))[0];

// Fetch last 5 orders
$orders = mysqli_query(
    $conn,
    "SELECT orders.*, books.title AS book_title
     FROM orders 
     JOIN books ON orders.book_id = books.id
     WHERE customer_id=$customer_id
     ORDER BY order_date DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard - BAS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        body { background: #f5f7fa; }
        .box-card {
            padding: 20px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">📚 BAS Customer Dashboard</span>
        <div>
            <a href="profile.php" class="btn btn-outline-light">My Profile</a>
            <a href="/BAS/logout.php" class="btn btn-outline-light">Logout</a>

        </div>
    </div>
</nav>

<div class="container mt-4 text-center">

    <h2>Welcome, <?= $customer['name']; ?> 👋</h2>
    <p class="text-muted">Your personalized dashboard with your activity</p>

    <div class="row mt-4">

        <div class="col-md-4 mb-3">
            <div class="box-card">
                <h5>📚 Browse Books</h5>
                <a href="browse_books.php" class="btn btn-success mt-2 w-100">Go</a>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="box-card">
                <h5>🛒 View Cart</h5>
                <a href="cart.php" class="btn btn-success mt-2 w-100">Go</a>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="box-card">
                <h5>📦 My Orders</h5>
                <a href="orders.php" class="btn btn-success mt-2 w-100">Go</a>
            </div>
        </div>

    </div>

    <hr class="my-4">

    <h4>Your Recent Orders</h4>

    <?php if (mysqli_num_rows($orders) > 0): ?>
        <table class="table table-bordered bg-white mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Book</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <?php while ($o = mysqli_fetch_assoc($orders)): ?>
            <tr>
                <td><?= $o['book_title']; ?></td>
                <td><?= date("d-m-Y H:i", strtotime($o['order_date'])); ?></td>

                <td>
                    <?php
                    $s = strtolower($o['status']);
                    if ($s == 'pending') echo "<span class='status-badge bg-warning text-dark'>Pending</span>";
                    elseif ($s == 'processing') echo "<span class='status-badge bg-primary'>Processing</span>";
                    elseif ($s == 'shipped') echo "<span class='status-badge bg-info'>Shipped</span>";
                    elseif ($s == 'delivered') echo "<span class='status-badge bg-success'>Delivered</span>";
                    elseif ($s == 'cancelled') echo "<span class='status-badge bg-danger'>Cancelled</span>";
                    ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

    <?php else: ?>
        <div class="alert alert-info mt-3">No orders placed yet.</div>
    <?php endif; ?>

</div>

</body>
</html>

