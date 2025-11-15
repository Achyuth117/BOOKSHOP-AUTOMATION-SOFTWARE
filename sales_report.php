<?php 
include("includes/header.php");
include("../includes/db.php");
?>

<div style="margin-top:40px;"></div>
<h2>📊 Sales Report</h2>

<?php
// Filters
$from = $_GET['from'] ?? "";
$to   = $_GET['to'] ?? "";

// Base query
$query = "
SELECT orders.*, books.title, books.price 
FROM orders 
JOIN books ON orders.book_id = books.id 
WHERE orders.status = 'Delivered'
";

// Apply date filters
if (!empty($from) && !empty($to)) {
    $query .= " AND DATE(order_date) BETWEEN '$from' AND '$to'";
}

$query .= " ORDER BY orders.order_date DESC";

// Execute
$res = mysqli_query($conn, $query);

// Total sales calculation
$total_sales = 0;
$total_orders = 0;

$data = [];  

while ($row = mysqli_fetch_assoc($res)) {
    $total_orders++;
    $total_sales += ($row['price'] * $row['quantity']);
    $data[] = $row;
}
?>

<!-- 🔍 Filter Form -->
<form method="GET" class="row mb-4">
    <div class="col-md-4">
        <label>From Date</label>
        <input type="date" name="from" class="form-control" value="<?= $from ?>">
    </div>
    <div class="col-md-4">
        <label>To Date</label>
        <input type="date" name="to" class="form-control" value="<?= $to ?>">
    </div>
    <div class="col-md-4">
        <label>&nbsp;</label><br>
        <button class="btn btn-primary w-100">Filter</button>
    </div>
</form>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card p-3">
            <h5>Total Orders</h5>
            <h3><?= $total_orders ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h5>Total Sales</h5>
            <h3>₹<?= number_format($total_sales, 2) ?></h3>
        </div>
    </div>
</div>

<!-- Sales Table -->
<table class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>Order ID</th>
            <th>Book</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody>
        <?php if (empty($data)): ?>
            <tr><td colspan="6" class="text-center">No sales found</td></tr>
        <?php else: ?>
            <?php foreach ($data as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['title']) ?></td>
                <td>₹<?= number_format($s['price'], 2) ?></td>
                <td><?= $s['quantity'] ?></td>
                <td>₹<?= number_format($s['price'] * $s['quantity'], 2) ?></td>
                <td><?= date("d-m-Y H:i", strtotime($s['order_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php echo "</div></body></html>"; ?>

