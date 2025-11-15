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

// Count orders
$order_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE customer_id=$customer_id"))[0];

// Get last 5 orders
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
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        body { background: #f4f6f9; }
        .card-box {
            padding: 25px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        color: white;
    }
    </style>
</head>
<body>


<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">📚 Smart Book Shop</span>
        <div>
            <a href="profile.php" class="btn btn-outline-light">My Profile</a>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>


<div class="container mt-4">

    <h3>Hello, <?= $customer['name']; ?> 👋</h3>
    <p class="text-muted">Welcome to your dashboard</p>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card-box text-center">
                <h5>Total Orders</h5>
                <h2><?= $order_count; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <a href="orders.php" class="text-decoration-none text-dark">
                <div class="card-box text-center">
                    <h5>📦 View All Orders</h5>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="browse_books.php" class="text-decoration-none text-dark">
                <div class="card-box text-center">
                    <h5>📚 Browse Books</h5>
                </div>
            </a>
        </div>
    </div>

    <h4>Recent Orders</h4>

    <table class="table table-bordered bg-white">
        <tr>
            <th>Book</th>
            <th>Date</th>
            <th>Status</th>
        </tr>

        <?php while ($o = mysqli_fetch_assoc($orders)): ?>
        <tr>
            <td><?= $o['book_title'] ?></td>
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

</div>

</body>
</html>

