<?php
session_start();
include("includes/db.php");
// Handle book request submission
if (isset($_POST['request_book'])) {
    $book_name = mysqli_real_escape_string($conn, $_POST['requested_book']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    if (!empty($book_name) && !empty($name) && !empty($contact)) {
        $insert = "INSERT INTO book_requests (book_name, requester_name, contact) 
                   VALUES ('$book_name', '$name', '$contact')";
        if (mysqli_query($conn, $insert)) {
            $request_success = "✅ Your request for '$book_name' has been sent to the admin.";
        } else {
            $request_error = "⚠️ Something went wrong. Please try again.";
        }
    } else {
        $request_error = "Please fill all fields before submitting.";
    }
}

// Handle search
// at top of index.php after include db
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, trim($_GET['search']));
    $sql = "SELECT * FROM books WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%'";
    $books = mysqli_query($conn, $sql);

    // If exact match found but stock=0, increment request_count
    if ($books && mysqli_num_rows($books) > 0) {
        while ($b = mysqli_fetch_assoc($books)) {
            if ($b['stock'] <= 0) {
                // increment request_count for this book
                mysqli_query($conn, "UPDATE books SET request_count = request_count + 1 WHERE id = " . intval($b['id']));
            }
        }
        // re-run query to fetch fresh rows (optional)
        $books = mysqli_query($conn, $sql);
    } else {
        // no matches — the "request form" on page inserts into book_requests (already present)
    }
} else {
    $books = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT 12");
}

// Handle login requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Admin login
if (isset($_POST['admin_login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);

        $_SESSION['admin'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['role'] = $admin['role']; // NEW

        header("Location: admin/dashboard.php");
        exit();
    } else {
        $login_error = "❌ Invalid admin username or password!";
    }
}

    // Customer login
    if (isset($_POST['customer_login'])) {
        $mobile = $_POST['mobile'];
        $password = $_POST['password'];
        $query = "SELECT * FROM customers WHERE phone='$mobile' AND password='$password'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 1) {
            $cust = mysqli_fetch_assoc($result);
            $_SESSION['customer'] = $cust['email'];
            header("Location: customer/home.php");
            exit();
        } else {
            $login_error = "❌ Invalid mobile number or password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BAS - Smart Book Shop Browser</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
    .hero {
      background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                  url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f') no-repeat center center/cover;
      height: 60vh; display: flex; justify-content: center; align-items: center;
      color: white; text-align: center;
    }
    .hero h1 { font-size: 3rem; font-weight: 700; }
    .search-bar { margin-top: -30px; position: relative; z-index: 2; }
    .book-card img { height: 250px; object-fit: cover; border-radius: 10px 10px 0 0; }
    .book-card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.3s; }
    .book-card:hover { transform: scale(1.03); }
    .footer {
  background-color: #343a40;
  color: #fff;
  text-align: center;
  padding: 15px;
  margin-top: 50px;
  border-radius: 8px 8px 0 0;
}

    .book-card {
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease-in-out;
  overflow: hidden;
}

.book-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.book-card img {
  height: 250px;
  object-fit: cover;
  border-bottom: 2px solid #eee;
}

.card-body h5 {
  font-weight: 600;
  font-size: 1.1rem;
}

.container .row {
  justify-content: center;
}
/* ✨ Enhanced Search Bar Design */
.search-container {
  position: relative;
  z-index: 3;
}
body {
  padding-top: 80px; /* pushes content below fixed navbar */
}
.navbar-brand {
  font-size: 1.4rem;
  letter-spacing: 0.5px;
}
.modal-content {
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}
.btn-close {
  filter: brightness(0) invert(1);
}

.search-form {
  transition: 0.3s ease-in-out;
}

.search-input {
  padding: 15px 20px;
  font-size: 1.1rem;
  border-radius: 50px;
  background-color: transparent;
}

.search-input:focus {
  outline: none;
  box-shadow: none;
}

.search-form:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
}
.login-section {
  position: relative;
  z-index: 5;
}
.nav-tabs .nav-link.active {
  background-color: #0d6efd;
  color: #fff !important;
  border-radius: 10px 10px 0 0;
}
.card {
  border: none;
}

.search-form .btn {
  font-size: 1rem;
  background: linear-gradient(135deg, #007bff, #0056b3);
  border: none;
}

.search-form .btn:hover {
  background: linear-gradient(135deg, #0056b3, #007bff);
}
body {
  padding-top: 80px; /* prevent navbar overlap */
}
.navbar-brand {
  font-size: 1.4rem;
}
.btn {
  border-radius: 8px;
}

/* “No Results” message card */
.no-results-card {
  background-color: #fff;
  border: 2px dashed #ccc;
  border-radius: 12px;
  padding: 25px;
  text-align: center;
  color: #555;
  font-size: 1.1rem;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  margin-top: 30px;
}
.modal-body a {
  transition: 0.2s ease;
}

.modal-body a:hover {
  text-decoration: underline;
  opacity: 0.8;
}
.hero {
  height: 90vh;
  background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
              url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f') center/cover no-repeat;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  color: #fff;
  padding: 20px;
}

.hero h1 {
  font-size: 3rem;
  font-weight: 700;
}

.hero p {
  font-size: 1.25rem;
  margin-bottom: 20px;
}

.btn-lg {
  border-radius: 50px;
  transition: all 0.3s ease;
}

.btn-lg:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

@media (max-width: 576px) {
  .hero h1 {
    font-size: 2rem;
  }
  .hero p {
    font-size: 1rem;
  }
}


}
  </style>
</head>
<body>

<!-- 🔹 Top Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="index.php">📚 Smart Book Shop</a>

    <div class="ms-auto d-flex align-items-center">
      <?php if(isset($_SESSION['admin'])): ?>
        <span class="me-3 text-primary fw-semibold">👑 Welcome, <?= $_SESSION['admin']; ?></span>
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>

      <?php elseif(isset($_SESSION['customer'])): ?>
        <span class="me-3 text-success fw-semibold">👋 Welcome, <?= $_SESSION['customer']; ?></span>
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>

      <?php else: ?>
      
      <?php endif; ?>
    </div>
  </div>
</nav>



<!-- 🔹 Login Forms Below Navbar -->
<section class="tab-content mt-5 pt-5" id="loginTabsContent">
<!-- 🔸 Admin Login Modal -->
<div class="modal fade" id="adminLogin" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Admin Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="admin_login" class="btn btn-primary w-100">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- 🔸 Customer Login Modal -->
<div class="modal fade" id="customerLogin" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Customer Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Mobile Number</label>
            <input type="text" name="mobile" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="text-center mt-2">
            <a href="customer/forgot_password.php" class="text-danger d-block">Forgot Password?</a>
            <a href="customer/register.php" class="text-primary d-block mt-1">New User? Register here</a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="customer_login" class="btn btn-success w-100">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Hero -->
<!-- 🌟 Hero Section with Centered Responsive Login Buttons -->
<section class="hero d-flex flex-column justify-content-center align-items-center text-center">
  <div class="text-center text-white">
    <h1 class="fw-bold display-5 mb-2">📚 Smart Book Shop Browser</h1>
    <p class="lead mb-4">Find your favorite books instantly!</p>

    <!-- 🔹 Centered Login Buttons -->
    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
      <button class="btn btn-success btn-lg px-4 py-2 shadow-lg" data-bs-toggle="modal" data-bs-target="#customerLogin">
        👤 Customer Login
      </button>
      <button class="btn btn-primary btn-lg px-4 py-2 shadow-lg" data-bs-toggle="modal" data-bs-target="#adminLogin">
        🔑 Admin Login
      </button>
    </div>
  </div>
</section>


<!-- Search Bar -->
<!-- Beautiful Search Section -->
<div class="container-fluid mt-n5 search-container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <form class="search-form shadow-lg rounded-pill d-flex align-items-center bg-white px-3" method="GET" action="">
        <input type="text" 
               name="search" 
               class="form-control border-0 shadow-none search-input"
               placeholder="🔍 Search your next great read..." 
               value="<?= htmlspecialchars($search_query); ?>">
        <button class="btn btn-primary rounded-pill px-4 fw-bold" type="submit">Search</button>
      </form>
    </div>
  </div>
</div>


<!-- Books Display -->
<div class="container mt-5">
  <div class="row">
    <?php if (mysqli_num_rows($books) > 0): ?>
      <?php
$seen = []; // prevent duplicates
while ($book = mysqli_fetch_assoc($books)):
    if (in_array($book['id'], $seen)) continue; 
    $seen[] = $book['id'];
?>
 <div class="col-md-3 col-sm-6 mb-4 d-flex align-items-stretch">
  <a href="book_details.php?id=<?= $book['id']; ?>" class="text-decoration-none text-dark w-100">
    <div class="card book-card h-100">

   <?php
$imgPath = "images/books/" . $book['image'];
if (!file_exists($imgPath) || empty($book['image'])) {
    $imgPath = "images/noimage.png";
}


// If image is a URL, use it directly. If not, load from local uploads folder.
if (strpos($image, 'http') === 0) {
    $img_src = $image;
} else {
    $img_src = "uploads/books/" . $image;
}
?>
<img src="<?= htmlspecialchars($book['image'] ?: 'images/noimage.png'); ?>" 
     alt="<?= htmlspecialchars($book['title']); ?>" class="card-img-top">



      <div class="card-body text-center">
        <h5 class="card-title mb-2"><?= htmlspecialchars($book['title']); ?></h5>
        <p class="text-muted mb-1">👤 <?= htmlspecialchars($book['author']); ?></p>
        <p class="fw-bold text-success">₹<?= number_format($book['price'], 2); ?></p>
      </div>

    </div>
  </a>
</div>

<?php endwhile; ?>

    <?php else: ?>
    <div class="col-12">
  <div class="no-results-card">
    <h5>📖 No books found for "<b><?= htmlspecialchars($search_query); ?></b>"</h5>
    <p>Would you like to request this book from the admin?</p>

    <form method="POST" class="mt-3">
      <input type="hidden" name="requested_book" value="<?= htmlspecialchars($search_query); ?>">
      <div class="row justify-content-center">
        <div class="col-md-3 mb-2">
          <input type="text" name="name" class="form-control" placeholder="Your name" required>
        </div>
        <div class="col-md-3 mb-2">
          <input type="text" name="contact" class="form-control" placeholder="Email or mobile" required>
        </div>
        <div class="col-md-2 mb-2">
          <button type="submit" name="request_book" class="btn btn-primary w-100">Send Request</button>
        </div>
      </div>
    </form>

    <?php if (isset($request_success)): ?>
      <div class="alert alert-success mt-3 text-center"><?= $request_success; ?></div>
    <?php elseif (isset($request_error)): ?>
      <div class="alert alert-danger mt-3 text-center"><?= $request_error; ?></div>
    <?php endif; ?>
  </div>
</div>


    <?php endif; ?>
  </div>
</div>


<!-- Footer -->
<div class="footer">
  © 2025 Smart Book Shop Browser | Designed by <b>TEAM-5</b>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

