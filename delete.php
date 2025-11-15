<?php
include("../includes/db.php");
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_books.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch book details (to delete image)
$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM books WHERE id=$id"));

if ($book) {
    $image = $book['image'];

    // Delete book from DB
    mysqli_query($conn, "DELETE FROM books WHERE id=$id");

    // Delete image if exists, not empty, and not noimage.png
    $path = "../images/books/" . $image;
    if (!empty($image) && $image !== "noimage.png" && file_exists($path)) {
        unlink($path);
    }

    header("Location: manage_books.php?msg=deleted");
    exit();
} else {
    header("Location: manage_books.php?msg=notfound");
    exit();
}
?>

