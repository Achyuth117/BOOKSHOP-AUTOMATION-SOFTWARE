<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit();
}

$customer_email = $_SESSION['customer'];
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE email='$customer_email'"));
$customer_id = $customer['id'];

$query = "
SELECT orders.*, books.title AS book_title, books.author, books.price
FROM orders
JOIN books ON orders.book_id = books.id
WHERE orders.customer_id = $customer_id
ORDER BY orders.order_date DESC
";
$orders = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders - BAS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="home.php">📚 Smart Book Shop</a>
    <div>
      <a href="browse_books.php" class="btn btn-outline-light">Browse Books</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h3 class="text-center mb-4">📦 My Orders</h3>

  <?php if (mysqli_num_rows($orders) > 0): ?>
    <table class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>Book</th>
          <th>Author</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Order Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
        <tr>
          <td><?= $order['book_title']; ?></td>
          <td><?= $order['author']; ?></td>
          <td>₹<?= $order['price']; ?></td>
          <td><?= $order['quantity']; ?></td>
          <td><?= date("d-m-Y H:i", strtotime($order['order_date'])); ?></td>
          <td>
           <?php 
$status = strtolower($order['status']);

if ($status == 'pending') 
    echo '<span class="badge bg-warning text-dark">Pending</span>';
elseif ($status == 'processing') 
    echo '<span class="badge bg-primary">Processing</span>';
elseif ($status == 'shipped') 
    echo '<span class="badge bg-info">Shipped</span>';
elseif ($status == 'delivered') 
    echo '<span class="badge bg-success">Delivered</span>';
elseif ($status == 'cancelled') 
    echo '<span class="badge bg-danger">Cancelled</span>';
else
    echo '<span class="badge bg-secondary">Unknown</span>';
?>

          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-warning text-center">You haven’t placed any orders yet!</div>
  <?php endif; ?>
</div>

</body>
</html>

