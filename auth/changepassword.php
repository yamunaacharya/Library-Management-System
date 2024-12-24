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
</head>
<body>
    <h2>Change Password</h2>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <p>Password updated successfully!</p>
    <?php endif; ?>
    <form method="POST" action="">
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
