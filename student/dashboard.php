<?php
require '../includes/config.php';
session_start();
$studentEmail = $_SESSION['student_email'];

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
    <title>Library Management System - Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dropdown {
            position: relative;
        }
        .dropdown ul {
            display: none;
            position: absolute;
            background-color: #2C3E50;
            padding: 0;
            margin: 0;
            /* border: 1px solid #ddd; */
            min-width: 200px;
            z-index: 1000; 
        }
        .dropdown ul li a {
            text-decoration: none;
            display: block;
            padding: 10px;
            color: white;
        }
        .dropdown:hover ul {
            display: block;
        }
        .sidebar nav ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar nav ul li {
            margin-bottom: 10px;
        }
        .dropdown:hover + li {
            margin-top: 70px;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h1>
            <h1>Student Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="issuedbook.php">Issued Books</a></li>
                    <li class="dropdown">
                        <a href="#">Category</a>
                        <ul>
                            <li><a href="category.php">Title</a></li>
                            <li><a href="category.php">Authors</a></li>
                            <li><a href="category.php">ISBN</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </aside>
    </div>
</body>
</html>
