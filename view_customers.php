<?php include("includes/header.php"); include("../includes/db.php"); ?>
<div style="margin-top: 40px;"></div>

<h2>👥 Customers</h2>

<table class="table table-bordered bg-white">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th></tr>

<?php
$res = mysqli_query($conn, "SELECT * FROM customers ORDER BY id DESC");
while($c = mysqli_fetch_assoc($res)):
?>
<tr>
  <td><?= $c['id']; ?></td>
  <td><?= htmlspecialchars($c['name'] ?? $c['email']); ?></td>
  <td><?= htmlspecialchars($c['email']); ?></td>
  <td><?= htmlspecialchars($c['phone'] ?? '—'); ?></td>
  <td><?= $c['created_at'] ?? '—'; ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php echo "</div></body></html>"; ?>

