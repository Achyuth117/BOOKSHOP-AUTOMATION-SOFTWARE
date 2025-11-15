<?php include("includes/header.php"); include("../includes/db.php"); ?>
<div style="margin-top: 40px;"></div>
<h2>⚠ Low Stock Report</h2>

<?php
$threshold = 5;
$res = mysqli_query($conn, "SELECT * FROM books WHERE stock <= $threshold ORDER BY stock ASC");
?>

<table class="table bg-white">
  <thead><tr><th>ID</th><th>Title</th><th>Stock</th></tr></thead>
  <tbody>
    <?php while($b = mysqli_fetch_assoc($res)): ?>
      <tr>
        <td><?= $b['id']; ?></td>
        <td><?= htmlspecialchars($b['title']); ?></td>
        <td><?= (int)$b['stock']; ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php echo "</div></body></html>"; ?>

