<?php
require '../includes/config.php'; 
session_start();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_password = $row['password'];

        if ($old_password === $current_password) { // Change this logic for hashed passwords
            if ($new_password === $confirm_password) {
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $new_password, $user_id);

                if ($update_stmt->execute()) {
                    // Redirect to the same page with a success message
                    header("Location: changepassword.php?success=1");
                    exit;
                } else {
                    echo "Error updating password. Please try again.";
                }
            } else {
                echo "New password and confirmation do not match.";
            }
        } else {
            echo "Old password is incorrect.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <style>
      /* Style for the entire page */
body {
    font-family: Arial, sans-serif;
    background-color: #34495e;
    color: #333;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Container for the form */
form {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
    text-align: center;
}

/* Heading styling */
h1 {
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
}

/* Styling for form labels */
label {
    font-size: 16px;
    color: #555;
    text-align: left;
    display: block;
    margin-bottom: 5px;
}

/* Styling for input fields */
input[type="password"] {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-bottom: 15px;
    box-sizing: border-box;
}

/* Styling for the submit button */
button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #0056b3;
}

/* Styling for the success message */
p {
    font-size: 16px;
    color: green;
    margin-top: 20px;
}

    </style>
</head>
<body>
    
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <p>Password updated successfully!</p>
    <?php endif; ?>
    <form method="POST" action="">
    <h1>Change Password</h1>
        <label for="old_password">Old Password:</label><br>
        <input type="password" id="old_password" name="old_password" required><br><br>

        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br><br>

        <label for="confirm_password">Confirm New Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <button type="submit">Change Password</button>
    </form>
</body>
</html>
