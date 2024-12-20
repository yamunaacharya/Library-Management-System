<?php
require '../includes/config.php';

session_start();

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    echo "<script>alert('You are not authorized to add users.'); window.location.href='dashboard.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($password)) {
        echo "<script>alert('Password cannot be empty'); window.history.back();</script>";
        exit;
    }

    $role = 'Librarian';

    $query = "INSERT INTO users (fullname, email, role, password, phone) 
              VALUES ('$fullname', '$email', '$role', '$password', '$phone')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Librarian added successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management - Add Librarian</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h1>Library Admin</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="adduser.php">Add Librarian</a></li>
                    <li><a href="managelibrarian.php">Manage Librarian</a></li>
                    <li><a href="#">Reports</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="dashboard-header">
                <h2 class="text-center">Add New Librarian</h2>
            </header>
            <form method="POST" class="form">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Full name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <input type="text" class="form-control" value="Librarian" disabled>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone number" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Librarian</button>
            </form>
        </main>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
