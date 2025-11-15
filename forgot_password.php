<?php
// customer/forgot_password.php
session_start();
include("../includes/db.php");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Check user exists
    $stmt = $conn->prepare("SELECT id, name FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // Generate secure token and its hash
        $token = bin2hex(random_bytes(32));           // raw token to send via email
        $token_hash = hash('sha256', $token);         // store hash in DB
        $expires_at = date("Y-m-d H:i:s", time() + 3600); // 1 hour validity

        // Store in DB
        $upd = $conn->prepare("UPDATE customers SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $upd->bind_param("sss", $token_hash, $expires_at, $email);
        $upd->execute();

        // Build reset link
        $host = $_SERVER['HTTP_HOST'];
        // if your BAS isn't in root, adjust path accordingly
        $base = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
        $reset_link = "http://{$host}{$base}/reset_password.php?token={$token}";

        // Email content
        $subject = "BAS Password Reset Request";
        $message = "Hello " . htmlspecialchars($user['name']) . ",\n\n";
        $message .= "We received a request to reset your password for your BAS account.\n";
        $message .= "Click the link below to reset your password. This link will expire in 1 hour.\n\n";
        $message .= $reset_link . "\n\n";
        $message .= "If you did not request a password reset, please ignore this email.\n\nRegards,\nSmart Book Shop Browser";

        // Use proper headers. Replace From with your domain email
        $headers = "From: no-reply@yourdomain.com\r\n";
        $headers .= "Reply-To: no-reply@yourdomain.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Send mail (recommend using SMTP/PHPMailer on production)
        mail($email, $subject, $message, $headers);

        // Inform user
        $msg = "If this email exists in our system, a password reset link has been sent. Check your inbox.";
    } else {
        // keep message vague for privacy
        $msg = "If this email exists in our system, a password reset link has been sent. Check your inbox.";
    }
    $stmt->close();
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h3 class="text-center">Forgot Password</h3>

  <?php if ($msg): ?>
    <div class="alert alert-info"><?= htmlspecialchars($msg); ?></div>
  <?php endif; ?>

  <form method="post" style="max-width:420px;margin:30px auto;">
    <input type="email" name="email" class="form-control mb-3" placeholder="Enter your registered email" required>
    <button class="btn btn-primary w-100">Send Reset Link</button>
  </form>
</div>
</body>
</html>

