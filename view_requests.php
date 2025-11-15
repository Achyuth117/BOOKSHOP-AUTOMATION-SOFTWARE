<?php include("includes/header.php"); include("../includes/db.php"); ?>
<div style="margin-top: 40px;"></div>

<h2>📩 Book Requests</h2>

<table class="table table-bordered bg-white">
<tr><th>ID</th><th>Book</th><th>Name</th><th>Contact</th><th>Time</th></tr>

<?php
$res = mysqli_query($conn, "SELECT * FROM book_requests ORDER BY id DESC");
while($r = mysqli_fetch_assoc($res)):
?>
<tr>
  <td><?= $r['id']; ?></td>
  <td><?= htmlspecialchars($r['book_name']); ?></td>
  <td><?= htmlspecialchars($r['requester_name']); ?></td>
  <td><?= htmlspecialchars($r['contact']); ?></td>
  <td><?= $r['created_at'] ?? '—'; ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php echo "</div></body></html>"; ?>

