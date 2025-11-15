<?php
session_start();
session_unset();
session_destroy();

// Go to homepage after logout
header("Location: /BAS/index.php");
exit();
?>

