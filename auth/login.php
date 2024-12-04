<?php
require '../includes/config.php';

// Start session at the beginning
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format and ensure fields are not empty
    if (!empty($email) && !empty($password) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare SQL query to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM usersss WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Compare the entered password with the stored password
            if ($password === $user['password']) { // No hashing used here as per your request
                // Store user information in session
                $_SESSION['user_id'] = $user['id'];  // Store user ID
                $_SESSION['role'] = $user['role'];   // Store user role
                $_SESSION['fullname'] = $user['fullname']; // Store user name for personalization

                // Redirect based on user role
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");  // Admin dashboard
                } elseif ($user['role'] === 'student') {
                    header("Location: ../student/dashboard.php"); // Student dashboard
                } elseif ($user['role'] === 'librarian') {
                    header("Location: ../librarian/dashboard.php"); // librarian dashboard
                } 
                exit;
            } else {
                echo "<script>alert('Incorrect password');</script>";
            }
        } else {
            echo "<script>alert('User not found');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password format');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Login</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign Up here</a></p>
    </div>
</body>
</html>
