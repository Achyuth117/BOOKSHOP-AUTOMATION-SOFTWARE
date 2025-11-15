<?php include("includes/header.php"); include("../includes/db.php"); ?>
<div style="margin-top: 40px;"></div>

<h2 class="fw-bold mb-4">📄 View Orders</h2>

<?php
$query = "
SELECT 
    orders.id,
    orders.customer_id,
    orders.book_id,
    orders.quantity,
    orders.order_date,
    orders.status,
    customers.name AS customer_name,
    customers.email AS customer_email,
    customers.phone AS customer_phone,
    customers.address AS customer_address,
    books.title AS book_title,
    books.price
FROM orders
JOIN customers ON orders.customer_id = customers.id
JOIN books ON orders.book_id = books.id
ORDER BY orders.id DESC
";

$orders = mysqli_query($conn, $query);
?>

<table class="table table-bordered table-striped bg-white shadow-sm align-middle">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Book</th>
    <th>Qty</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
</tr>
</thead>

<tbody>
<?php while ($o = mysqli_fetch_assoc($orders)): ?>
<tr>
    <td><?= $o['id']; ?></td>

    <td>
        <b><?= $o['customer_name']; ?></b><br>
        <small><?= $o['customer_email']; ?></small><br>
        <small>📞 <?= $o['customer_phone']; ?></small><br>
        <small>🏠 <?= $o['customer_address']; ?></small>
    </td>

    <td><?= $o['book_title']; ?></td>

    <td><?= $o['quantity']; ?></td>

    <td>₹<?= number_format($o['price'] * $o['quantity'], 2); ?></td>

    <td>
        <?php 
        $s = strtolower($o['status']);
        if ($s == 'pending') echo "<span class='badge bg-warning text-dark'>Pending</span>";
        elseif ($s == 'processing') echo "<span class='badge bg-primary'>Processing</span>";
        elseif ($s == 'shipped') echo "<span class='badge bg-info'>Shipped</span>";
        elseif ($s == 'delivered') echo "<span class='badge bg-success'>Delivered</span>";
        elseif ($s == 'cancelled') echo "<span class='badge bg-danger'>Cancelled</span>";
        ?>
    </td>

    <td><?= date("d-m-Y H:i", strtotime($o['order_date'])); ?></td>
    

</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php echo "</div></body></html>"; ?>

