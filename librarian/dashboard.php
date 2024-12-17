<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: ../login.php");
    exit;
}

$fullname = $_SESSION['fullname'];
$profilePic = "../assets/images/profile.jpg"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: white;
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 20px 10px;
            position: fixed;
            height: 100%;
        }

        .sidebar h1 {
            font-size: 22px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar nav ul {
            list-style: none;
        }

        .sidebar nav ul li {
            margin: 20px 0;
        }

        .sidebar nav ul li a {
            color: #ddd;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;

        }

        .sidebar nav ul li a:hover,
        .sidebar nav ul li a.active {
            background-color: #17a2b8;
            color: #fff;
        }

        .header {
            position: fixed;
            top: 0;
            left: 250px;
            width: calc(100% - 250px);
            height: 70px;
            background-color: #ffffff;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .header .header-right {
            display: flex;
            align-items: center;
            gap: 15px; 
        }

        .header .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .header .profile span {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .header .logout-btn {
            background-color: blue;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        .header .logout-btn:hover {
            background-color: red;
        }

        .main-content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 30px;
        }

        .main-content h2 {
            font-size: 24px;
            color: #343a40;
            margin-bottom: 20px;
        }

        .main-content p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
    </style>
</head>
<body>
  
    <aside class="sidebar">
        <h1>Librarian Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="issuebook.php"><i class="fas fa-book"></i> Issue Book</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
        </nav>
    </aside>

    <header class="header">
        <div class="header-right">
            <div class="profile">
                <img src="<?php echo $profilePic; ?>" alt="Profile">
                <span><?php echo htmlspecialchars($fullname); ?></span>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

</body>
</html>
