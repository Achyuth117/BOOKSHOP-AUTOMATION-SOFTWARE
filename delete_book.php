<?php
include("../includes/db.php");
include("includes/header.php"); // ensures session
if (!isset($_GET['id'])) { header("Location: manage_books.php"); exit(); }
$id = intval($_GET['id']);
mysqli_query($conn, "DELETE FROM books WHERE id=$id");
header("Location: manage_books.php");
exit();

