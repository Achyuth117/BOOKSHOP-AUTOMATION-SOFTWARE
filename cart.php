<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit();
}

// Get customer details
$customer_email = $_SESSION['customer'];
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE email='$customer_email'"));
$customer_id = $customer['id'];

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add book to cart
if (isset($_POST['add'])) {
    $book_id = $_POST['book_id'];
    if (!in_array($book_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $book_id;
    }
}

// Place order
if (isset($_POST['place_order'])) {
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $book_id) {
            mysqli_query($conn, "INSERT INTO orders (customer_id, book_id, quantity, status) VALUES ($customer_id, $book_id, 1, 'Pending')");
        }
        $_SESSION['cart'] = []; // clear cart after placing order
        $success = "Order placed successfully!";
    } else {
        $error = "Your cart is empty!";
    }
}

// Fetch cart items
$book_list = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(",", $_SESSION['cart']);
    $query = "SELECT * FROM books WHERE id IN ($ids)";
    $book_list = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Cart - BAS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="home.php">📚 Smart Book Shop</a>
    <div>
      <a href="browse_books.php" class="btn btn-outline-light">Browse More</a>
    
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h3 class="text-center mb-4">🛒 Your Cart</h3>

  <?php if(isset($success)): ?>
    <div class="alert alert-success text-center"><?= $success; ?></div>
  <?php endif; ?>
  <?php if(isset($error)): ?>
    <div class="alert alert-danger text-center"><?= $error; ?></div>
  <?php endif; ?>

  <?php if (empty($_SESSION['cart'])): ?>
    <div class="alert alert-warning text-center">Your cart is empty!</div>
  <?php else: ?>
    <form method="POST">
      <table class="table table-bordered text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Book</th>
            <th>Author</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $total = 0;
          while ($book = mysqli_fetch_assoc($book_list)): 
            $total += $book['price'];
          ?>
          <tr>
            <td><?= $book['title']; ?></td>
            <td><?= $book['author']; ?></td>
            <td>₹<?= $book['price']; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="2" class="text-end">Total:</th>
            <th>₹<?= $total; ?></th>
          </tr>
        </tfoot>
      </table>

      <div class="text-center">
        <button type="submit" name="place_order" class="btn btn-success px-4">Place Order</button>
      </div>
    </form>
  <?php endif; ?>
</div>

</body>
</html>

