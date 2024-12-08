<?php
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];  
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = 'student'; // Default role is 'student'

    // Validate inputs
    if (!empty($fullname) && !empty($password) && !empty($email) && !empty($phone) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare the SQL statement to insert data
        $stmt = $conn->prepare("INSERT INTO usersss (fullname, password, email, phone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $password, $email, $phone, $role);

        // Execute the query
        if ($stmt->execute()) {
            echo "<script>alert('Successfully registered');</script>";
            header("Location: login.php");  // Redirect to login page
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
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Signup</h1>
        <form method="POST">
            <!-- Full Name -->
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name" required>
            </div>
            
            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <!-- Phone -->
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>
            
           <!-- Role (hidden input) -->
           <input type="hidden" name="role" value="student">
           
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>

        <p class="text-center">Already have an account? <a href="login.php">Login Here</a></p>
    </div>
</body>
</html>
