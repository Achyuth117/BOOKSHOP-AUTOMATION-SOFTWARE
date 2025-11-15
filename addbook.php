<?php 
include("includes/header.php"); 
include("../includes/db.php"); 
?>
<div style="margin-top: 40px;"></div>

<h2>➕ Add Book</h2>

<?php
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title  = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $price  = floatval($_POST['price']);
    $stock  = intval($_POST['stock']);

    // DEFAULT IMAGE
    $imageName = "noimage.png";

    // If image uploaded
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = "../images/books/" . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $msg = "⚠ Image upload failed. Using default image.";
            $imageName = "noimage.png";
        }
    }

    // Insert book into DB
    $sql = "INSERT INTO books (title, author, price, stock, image)
            VALUES ('$title', '$author', '$price', '$stock', '$imageName')";

    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Book added successfully!";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<?php if($msg): ?>
<div class="alert alert-info"><?= $msg; ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="mt-3">
    <input name="title" class="form-control mb-2" placeholder="Book Title" required>
    <input name="author" class="form-control mb-2" placeholder="Author" required>
    <input name="price" class="form-control mb-2" placeholder="Price" required>
    <input name="stock" class="form-control mb-2" placeholder="Stock" required>

    <label class="mb-1">Upload Book Image (optional):</label>
    <input type="file" name="image" class="form-control mb-3">

    <button class="btn btn-primary w-25">Add Book</button>
</form>

<?php echo "</div></body></html>"; ?>

