<?php
require '../includes/config.php'; // Include your database configuration

// Start session
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get email and password from POST data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (!empty($email) && !empty($password) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Fetch user data based on the email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Compare the plain text password
            if ($password === $user['password']) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = strtolower($user['role']);
                $_SESSION['fullname'] = $user['fullname'];

                // Redirect based on user role
                switch ($_SESSION['role']) {
                    case 'student':
                        header("Location: ../student/dashboard.php");
                        break;
                    case 'admin':
                        header("Location: ../admin/dashboard.php");
                        break;
                    case 'librarian':
                        header("Location: ../librarian/dashboard.php");
                        break;
                    default:
                        $error_message = "Unknown role. Please contact support.";
                        break;
                }
                exit;
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "Email not found.";
        }
    } else {
        $error_message = "Invalid email or password format.";
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
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

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
        <p class="text-center"><a href="forgot-password.php">Forgot password?</a></p>
    </div>
</body>
</html>
