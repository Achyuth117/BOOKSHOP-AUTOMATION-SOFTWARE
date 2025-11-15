<?php include("includes/header.php"); include("../includes/db.php"); ?>
<div style="margin-top: 40px;"></div>
<?php if(isset($_GET['msg']) && $_GET['msg']=="deleted"): ?>
    <div class="alert alert-success">Book deleted successfully.</div>
<?php endif; ?>

<?php
function img($name){
    $p = "../images/books/" . $name;
    return file_exists($p) ? $p : "../images/noimage.png";
}
?>
<img src="<?= img($b['image']) ?>" width="70">

<h2>📚 Manage Books</h2>

<div class="mb-3">
  <a class="btn btn-success" href="addbook.php">Add New Book</a>
</div>

<?php
// Pagination
$limit = 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page-1)*$limit;

$total_res = mysqli_query($conn, "SELECT COUNT(*) FROM books");
$total = mysqli_fetch_row($total_res)[0];
$pages = ceil($total / $limit);

$res = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>

<table class="table table-bordered bg-white">
  <thead>
    <tr><th>ID</th><th>Cover</th><th>Title</th><th>Author</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php while($b = mysqli_fetch_assoc($res)): ?>
      <tr>
        <td><?= $b['id']; ?></td>
        <td><img src="<?= htmlspecialchars($b['image']); ?>" width="60" onerror="this.src='https://via.placeholder.com/60';"></td>
        <td><?= htmlspecialchars($b['title']); ?></td>
        <td><?= htmlspecialchars($b['author']); ?></td>
        <td>₹<?= number_format($b['price'],2); ?></td>
        <td><?= (int)$b['stock']; ?></td>
        <td>
          <a class="btn btn-sm btn-warning" href="edit_book.php?id=<?= $b['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="delete_book.php?id=<?= $b['id']; ?>" onclick="return confirm('Delete this book?');">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<nav>
  <ul class="pagination">
    <?php for($p=1;$p<=$pages;$p++): ?>
      <li class="page-item <?= $p==$page?'active':'' ?>"><a class="page-link" href="?page=<?= $p; ?>"><?= $p; ?></a></li>
    <?php endfor; ?>
  </ul>
</nav>


<?php echo "</div></body></html>"; ?>

