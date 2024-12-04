<?php
require '../includes/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $password = $_POST['password']; // Plain password from form

    // Check if password is provided
    if (empty($password)) {
        die("<script>alert('Password cannot be empty'); window.history.back();</script>");
    }

    // Sanitize inputs to avoid SQL injection
    $fullname = mysqli_real_escape_string($conn, $fullname);
    $email = mysqli_real_escape_string($conn, $email);
    $role = mysqli_real_escape_string($conn, $role);
    $phone = mysqli_real_escape_string($conn, $phone);
    $password = mysqli_real_escape_string($conn, $password); // Plain password sanitized

    // Prepare and execute the query
    $stmt = $conn->prepare("INSERT INTO usersss (fullname, email, role, password, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $fullname, $email, $role, $password, $phone);

    if ($stmt->execute()) {
        echo "<script>alert('User successfully added!'); window.location.href='adashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> -->
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h1>Library Admin</h1>
            <nav>
            <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="adduser.php">Users</a></li>
                    <li><a href="add_books.php">AddBooks</a></li>
                    <li><a href="displaybooks.php">Books Details</a></li>
                    <li><a href="#">Reports</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="dashboard-header">
            <h2 class="text-center">Add New User</h2>
            </header>
            <div class="container mt-5">
        
        <form method="POST">
            <!-- Full Name -->
            <div class="mb-3">
                <label for="fullname">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
            </div>

            <!-- Role -->
            <div class="mb-3">
                <label for="role" class="form-label">Select Role</label>
                <select class="form-control" name="role" id="role" required>
                    <option value="Librarian">Librarian</option>
                    <option value="student" selected>Student</option>
                </select>
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter phone number" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-block">Add User</button>
        </form>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
        </main>
    </div>
</body>
</html>