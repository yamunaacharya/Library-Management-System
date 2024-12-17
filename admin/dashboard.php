<?php
require '../includes/config.php';
session_start();
$adminEmail = $_SESSION['fullname'];

function sanitize($data) {
    global $conn;
    return htmlspecialchars(mysqli_real_escape_string($conn, $data));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h1>Admin Dashboard <?php echo $adminEmail ?></h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="adduser.php">Add Users</a></li>
                    <li><a href="add_books.php">Add Books</a></li>
                    <li><a href="managebooks.php">Manage Books</a></li>
                    <li><a href="managelibrarian.php">Manage Librarian</a></li>
                    <li><a href="#">Reports</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </nav>
        </aside>
        
    </div>
</body>
</html>