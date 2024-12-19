<?php
require '../includes/config.php';

// Start session
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        $stmt = $conn->prepare("SELECT * FROM usersss WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) { 

                // Store user information in session
                $_SESSION['user_id'] = $user['id'];  
                $_SESSION['role'] = $user['role'];   
                $_SESSION['fullname'] = $user['fullname'];


                if ($user['role'] === 'student') {
                    $_SESSION['student_email'] = $user['email']; 
                    header("Location: ../student/dashboard.php");
                } elseif ($user['role'] === 'admin') {
                    $_SESSION['admin_email'] = $user['email'];
                    header("Location: ../admin/dashboard.php");
                } elseif ($user['role'] === 'librarian') {
                    header("Location: ../librarian/dashboard.php");
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
        <div class="logo">LibraryHub</div>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Catalog</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">About</a></li>
        </ul>
    </nav>
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
        <p class="text-center">Don't have an account? <a href="signup.php">Sign Up here</a></p>
    </div>
</body>
</html>