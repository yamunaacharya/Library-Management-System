<?php
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];  
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = 'student'; 

    if (!empty($fullname) && !empty($password) && !empty($email) && !empty($phone) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO users (fullname, password, email, phone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $password, $email, $phone, $role);

        if ($stmt->execute()) {
            echo "<script>alert('Successfully registered');</script>";
            header("Location: login.php");  
            exit;
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Please fill all fields with valid data');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
        <div class="logo">LibraryHub</div>
    </nav>
    <div class="container mt-5">
        <h1 class="text-center">Signup</h1>
        <form method="POST">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>
            
           <input type="hidden" name="role" value="student">
           
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>

        <p class="text-center">Already have an account? <a href="login.php">Login Here</a></p>
    </div>
</body>
</html>