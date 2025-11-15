<?php
include("includes/header.php");
include("../includes/db.php");

if (!isset($_GET['order_id'])) {
    echo '<div style="margin-top:40px;" class="alert alert-info">No order selected. Use ?order_id=1</div>';
    echo "</div></body></html>";
    exit();
}

$id = intval($_GET['order_id']);

$query = "
SELECT 
    orders.*,
    customers.name AS customer_name,
    customers.email,
    customers.phone,
    customers.address,
    books.title AS book_title,
    books.price AS book_price
FROM orders
JOIN customers ON orders.customer_id = customers.id
JOIN books ON orders.book_id = books.id
WHERE orders.id = $id
";

$order = mysqli_fetch_assoc(mysqli_query($conn, $query));

if (!$order) {
    echo '<div style="margin-top:40px;" class="alert alert-danger">Order not found.</div>';
    echo "</div></body></html>";
    exit();
}
?>

<!---------------------- PRINT CSS ------------------------>
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #printArea, #printArea * {
        visibility: visible !important;
    }
    #printArea {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        padding: 20px;
        box-shadow: none !important;
    }
}
.timeline {
    display: flex;
    justify-content: space-between;
    margin: 25px 0;
}
.timeline-step {
    text-align: center;
    width: 25%;
    padding: 8px;
    border-bottom: 4px solid #ccc;
    font-size: 14px;
}
.timeline-step.active {
    border-color: #0d6efd;
    font-weight: bold;
    color: #0d6efd;
}
</style>

<div style="margin-top:40px;"></div>

<h2 class="fw-bold mb-4">🧾 Sales Receipt</h2>

<!-- PRINT + PDF BUTTONS -->
<div class="mb-3">
    <button onclick="window.print()" class="btn btn-secondary btn-sm">🖨 Print Receipt</button>

  
</div>

<!---------------- RECEIPT AREA ---------------->
<div id="printArea" class="card shadow p-4">

    <h3 class="fw-bold mb-3">Smart Book Shop – Invoice #<?= $order['id']; ?></h3>

    <p><strong>Customer:</strong> <?= $order['customer_name']; ?></p>
    <p><strong>Email:</strong> <?= $order['email']; ?></p>
    <p><strong>Phone:</strong> <?= $order['phone']; ?></p>
    <p><strong>Address:</strong> <?= $order['address']; ?></p>

    <hr>

    <h4 class="fw-bold">Order Details</h4>
    <p><strong>Book:</strong> <?= $order['book_title']; ?></p>
    <p><strong>Price:</strong> ₹<?= number_format($order['book_price'], 2); ?></p>
    <p><strong>Quantity:</strong> <?= $order['quantity']; ?></p>

    <h4 class="mt-3 fw-bold">Total: ₹<?= number_format($order['book_price'] * $order['quantity'], 2); ?></h4>

    <hr>

    <p><strong>Order Date:</strong> <?= date("d-m-Y H:i", strtotime($order['order_date'])); ?></p>
    <p><strong>Status:</strong> <?= ucfirst($order['status']); ?></p>

    <!-------------- TIMELINE UI ---------------->
    <?php 
    $steps = ["Pending", "Processing", "Shipped", "Delivered"];
    $current = strtolower($order['status']);
    ?>

    <h4 class="mt-4">Order Progress</h4>

    <div class="timeline">
        <?php foreach ($steps as $step): ?>
            <div class="timeline-step <?= $current == strtolower($step) ? 'active' : '' ?>">
                <?= $step ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php echo "</div></body></html>"; ?>

