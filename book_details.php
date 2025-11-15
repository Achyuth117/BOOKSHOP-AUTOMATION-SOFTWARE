<?php
session_start();
include("includes/db.php");

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM books WHERE id = $id";
$result = mysqli_query($conn, $query);
$book = mysqli_fetch_assoc($result);

if (!$book) {
    echo "<h3 class='text-center mt-5'>Book not found!</h3>";
    exit();
}

// Add to cart (for logged-in customers)
if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['customer'])) {
        $book_id = $book['id'];
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (!in_array($book_id, $_SESSION['cart'])) {
            $_SESSION['cart'][] = $book_id;
        }
        $msg = "✅ Book added to cart!";
    } else {
        $msg = "⚠️ Please login as customer to add books to your cart.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($book['title']); ?> - BAS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f8f9fa; font-family: "Poppins", sans-serif; }
    .book-img { border-radius: 10px; width: 100%; max-height: 450px; object-fit: cover; }
    .card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
  </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a href="index.php" class="navbar-brand">📚 Smart Book Shop</a>
    <div>
      <?php if(isset($_SESSION['customer'])): ?>
        <a href="customer/cart.php" class="btn btn-outline-light">🛒 My Cart</a>
        <a href="customer/logout.php" class="btn btn-outline-light">Logout</a>
      <?php else: ?>
        <a href="#customerLogin" data-bs-toggle="modal" class="btn btn-outline-light">Customer Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="row">
    <div class="col-md-5">
      <img src="<?= $book['image'] ?: 'https://via.placeholder.com/400x450?text=No+Image'; ?>" alt="Book" class="book-img">
    </div>
    <div class="col-md-7">
      <div class="card p-4">
        <h2><?= htmlspecialchars($book['title']); ?></h2>
        <h5 class="text-muted">By <?= htmlspecialchars($book['author']); ?></h5>
        <h4 class="text-success mt-3">₹<?= number_format($book['price'], 2); ?></h4>
        <p><b>Available Stock:</b> <?= $book['stock']; ?></p>
        <p><?= $book['description'] ?: '<em>No description available.</em>'; ?></p>

        <?php if(isset($msg)): ?>
          <div class="alert alert-info"><?= $msg; ?></div>
        <?php endif; ?>

        <form method="POST">
          <button type="submit" name="add_to_cart" class="btn btn-success btn-lg mt-2">Add to Cart</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<div class="footer mt-5 text-center p-3 bg-dark text-white">
  © 2025 Smart Book Shop Browser | Designed by <b>TEAM-5</b>
</div>

<!-- Optional Login Modal (if user clicks login on navbar) -->
<div class="modal fade" id="customerLogin" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="index.php">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Customer Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Mobile Number</label>
            <input type="text" name="mobile" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="customer_login" class="btn btn-success">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

