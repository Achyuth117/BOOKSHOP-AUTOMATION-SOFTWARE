<?php include("includes/header.php"); include("../includes/db.php"); ?>
<div style="margin-top: 40px;"></div>

<h2 class="fw-bold mb-4">📋 Manage Orders</h2>

<?php
// Update status
if (isset($_GET['mark']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['mark']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$id");
    header("Location: manage_orders.php");
    exit();
}

$query = "
SELECT 
    orders.*,
    customers.name AS customer_name,
    customers.email AS customer_email,
    customers.phone AS customer_phone,
    customers.address AS customer_address,
    books.title AS book_title,
    books.price AS book_price
FROM orders
JOIN customers ON orders.customer_id = customers.id
JOIN books ON orders.book_id = books.id
ORDER BY orders.id DESC
";
$res = mysqli_query($conn, $query);
?>

<table class="table table-bordered bg-white shadow-sm">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Book</th>
    <th>Qty</th>
    <th>Total</th>
    <th>Status</th>
    <th width="300">Actions</th>
</tr>
</thead>

<tbody>
<?php while($o = mysqli_fetch_assoc($res)): ?>
<?php
$s = strtolower($o['status']);
$badge = "<span class='badge bg-secondary'>Unknown</span>";

if ($s == 'pending') $badge = "<span class='badge bg-warning text-dark'>Pending</span>";
elseif ($s == 'processing') $badge = "<span class='badge bg-primary'>Processing</span>";
elseif ($s == 'shipped') $badge = "<span class='badge bg-info'>Shipped</span>";
elseif ($s == 'delivered') $badge = "<span class='badge bg-success'>Delivered</span>";
elseif ($s == 'cancelled') $badge = "<span class='badge bg-danger'>Cancelled</span>";
?>

<tr>
    <td><?= $o['id']; ?></td>

    <td>
        <b><?= htmlspecialchars($o['customer_name']); ?></b><br>
        <small><?= $o['customer_email']; ?></small><br>
        <small>📞 <?= $o['customer_phone']; ?></small><br>
        <small>🏠 <?= $o['customer_address']; ?></small>
    </td>

    <td><?= $o['book_title']; ?></td>

    <td><?= $o['quantity']; ?></td>

    <td>₹<?= number_format($o['book_price'] * $o['quantity'], 2); ?></td>

    <td><?= $badge; ?></td>

    <td>
        <a href="manage_orders.php?mark=Processing&id=<?= $o['id']; ?>" class="btn btn-sm btn-primary">Processing</a>
        <a href="manage_orders.php?mark=Shipped&id=<?= $o['id']; ?>" class="btn btn-sm btn-info text-white">Shipped</a>
        <a href="manage_orders.php?mark=Delivered&id=<?= $o['id']; ?>" class="btn btn-sm btn-success">Delivered</a>
        <a href="manage_orders.php?mark=Cancelled&id=<?= $o['id']; ?>" class="btn btn-sm btn-danger">Cancel</a>
        <a href="sales_receipt.php?order_id=<?= $o['id']; ?>" class="btn btn-sm btn-warning mt-1">
    🧾 View Receipt
</a>

    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php echo "</div></body></html>"; ?>

