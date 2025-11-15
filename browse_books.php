<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit();
}

$books = mysqli_query($conn, "SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Browse Books - BAS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="home.php">📚 Smart Book Shop</a>

  </div>
</nav>

<div class="container mt-4">
  <h3 class="text-center mb-4">Available Books</h3>
  <div class="row">
    <?php while ($book = mysqli_fetch_assoc($books)): ?>
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
          <img src="<?= $book['image']; ?>" class="card-img-top" alt="Book Image" style="height:250px; object-fit:cover;">
          <div class="card-body">
            <h5 class="card-title"><?= $book['title']; ?></h5>
            <p class="card-text">👤 <?= $book['author']; ?></p>
            <p class="card-text"><b>₹<?= $book['price']; ?></b></p>
            <form method="POST" action="cart.php">
              <input type="hidden" name="book_id" value="<?= $book['id']; ?>">
              <button type="submit" name="add" class="btn btn-success w-100">Add to Cart</button>
            </form>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

</body>
</html>

