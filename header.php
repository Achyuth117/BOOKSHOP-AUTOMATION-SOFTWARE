<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
body { background: #f5f6fa; }

.navbar {
    border-radius: 0 0 10px 10px;
}

.sidebar {
    width: 250px;
    height: 100vh;
    background: #212529;
    position: fixed;
    top: 0;
    padding-top: 70px;
    color: white;
    overflow-y: auto;
}

.sidebar a {
    display: block;
    padding: 12px 20px;
    color: #ddd;
    text-decoration: none;
    font-size: 15px;
}

.sidebar a:hover {
    background: #343a40;
    color: white;
}

.sidebar-title {
    padding: 10px 20px;
    font-size: 14px;
    text-transform: uppercase;
    opacity: .6;
}

.content {
    margin-left: 260px;
    padding: 25px;
}

.dropdown-container {
    display: none;
    background-color: #2f353a;
}

.dropdown-btn.active + .dropdown-container {
    display: block;
}
</style>

<script>
function toggleDropdown(id) {
    let element = document.getElementById(id);
    element.style.display = (element.style.display === "block") ? "none" : "block";
}
</script>

</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-dark bg-primary fixed-top px-3">
    <span class="navbar-brand">📚 Admin Panel</span>
    <div class="text-end">
        <span class="text-light fw-bold me-3"><?= $_SESSION['admin']; ?></span>
        <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">

    <div class="sidebar-title">Dashboard</div>
    <a href="dashboard.php">🏠 Home</a>

    <div class="sidebar-title">Books</div>
    <a href="manage_books.php">📚 Manage Books</a>
    <a href="update_inventory.php">📦 Update Inventory</a>
    <a href="low_stock_report.php">⚠ Low Stock Report</a>
<a href="sales_report.php">📊 Sales Report</a>

    <div class="sidebar-title">Orders</div>
    <a href="view_orders.php">🧾 View Orders</a>
    <a href="manage_orders.php">📋 Manage Orders</a>
    <a href="sales_receipt.php">🧾 Sales Receipt</a>

    <div class="sidebar-title">Users</div>
    <a href="view_customers.php">👥 View Customers</a>

    <div class="sidebar-title">Requests</div>
    <a href="view_requests.php">📩 Book Requests</a>

</div>

<!-- Main Content Wrapper -->
<div class="content">

