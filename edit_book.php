<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE id=$id"));

if (isset($_POST['update'])) {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // KEEP old image by default
    $image = $book['image'];

    // If a new file was uploaded
    if (!empty($_FILES['image']['name'])) {

        // New file name
        $imageName = time() . "_" . basename($_FILES['image']['name']);

        // Correct folder
        $target = "../images/books/" . $imageName;

        // Upload it
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {

            // delete old image if not default
            if (!empty($book['image']) && $book['image'] !== "noimage.png") {
                $old = "../images/books/" . $book['image'];
                if (file_exists($old)) unlink($old);
            }

            $image = $imageName; // Save only file name
        }
    }

    mysqli_query($conn, "UPDATE books 
        SET title='$title', author='$author', price='$price', stock='$stock', image='$image' 
        WHERE id=$id");

    header("Location: manage_books.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Book - BAS Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">

  <h3>Edit Book</h3>

  <form method="POST" enctype="multipart/form-data">

    <div class="mb-3">
      <label>Title</label>
      <input type="text" name="title" value="<?= htmlspecialchars($book['title']); ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Author</label>
      <input type="text" name="author" value="<?= htmlspecialchars($book['author']); ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Price</label>
      <input type="number" name="price" value="<?= $book['price']; ?>" class="form-control" step="0.01" required>
    </div>

    <div class="mb-3">
      <label>Stock</label>
      <input type="number" name="stock" value="<?= $book['stock']; ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Book Image</label><br>

      <?php 
      $img = "../images/books/" . $book['image'];
      if (!file_exists($img) || empty($book['image'])) {
          $img = "../images/noimage.png";
      }
      ?>

      <img src="<?=$img?>" width="80"><br><br>

      <input type="file" name="image" class="form-control">
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update</button>
    <a href="manage_books.php" class="btn btn-secondary">Cancel</a>
  </form>

</div>

</body>
</html>

