<?php
include("../includes/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $check = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered!";
    } else {
        $query = "INSERT INTO customers (name, email, password, phone, address) VALUES ('$name', '$email', '$password', '$phone', '$address')";
        if (mysqli_query($conn, $query)) {
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Registration - BAS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4 shadow-sm">
        <h4 class="text-center mb-3">Customer Registration</h4>

        <?php if(isset($error)): ?>
          <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        <?php if(isset($success)): ?>
          <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control">
          </div>
          <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control"></textarea>
          </div>
          <button type="submit" class="btn btn-success w-100">Register</button>
        </form>
<div class="text-center mt-3">
  Already have an account?
  <a href="../index.php" data-bs-toggle="modal" data-bs-target="#customerLogin" class="text-primary fw-semibold">
    Login here
  </a>
</div>


      </div>
    </div>
  </div>
</div>

</body>
</html>

