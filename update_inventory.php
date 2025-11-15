<?php include("includes/header.php"); include("../includes/db.php"); ?>
<div style="margin-top: 40px;"></div>

<h2>📦 Update Inventory</h2>

<?php
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $id = intval($_POST['book_id']);
    $stock = intval($_POST['stock']);
    mysqli_query($conn, "UPDATE books SET stock=$stock WHERE id=$id");
    $msg = "Stock updated.";
}
$books = mysqli_query($conn, "SELECT id,title,stock FROM books ORDER BY title");
?>

<?php if($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg); ?></div><?php endif; ?>

<table class="table bg-white">
<thead><tr><th>ID</th><th>Title</th><th>Stock</th><th>Action</th></tr></thead>
<tbody>
<?php while($b = mysqli_fetch_assoc($books)): ?>
<tr>
  <form method="post">
    <td><?= $b['id']; ?><input type="hidden" name="book_id" value="<?= $b['id']; ?>"></td>
    <td><?= htmlspecialchars($b['title']); ?></td>
    <td><input name="stock" value="<?= $b['stock']; ?>" class="form-control" style="width:100px"></td>
    <td><button name="update_stock" class="btn btn-primary btn-sm">Update</button></td>
  </form>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php echo "</div></body></html>"; ?>

