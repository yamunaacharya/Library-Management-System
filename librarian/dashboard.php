<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id']; 
$fullname = $_SESSION['fullname'];
$profilePic = "../assets/images/profile.jpg"; 

// Fetching user details from the database
$query = "SELECT * FROM users WHERE id = '$user_id' AND role = 'librarian'";
$result = mysqli_query($conn, $query);
$user_details = mysqli_fetch_assoc($result);

if (!$user_details) {
    echo "<script>alert('Error fetching user details');</script>";
    exit;
}

// Fetching report data
$total_students_query = "SELECT COUNT(*) AS total_students FROM users WHERE role = 'student'";
$total_students_result = mysqli_query($conn, $total_students_query);
$total_students = mysqli_fetch_assoc($total_students_result)['total_students'] ?? 0;

$total_books_query = "SELECT COUNT(*) AS total_books FROM books";
$total_books_result = mysqli_query($conn, $total_books_query);
$total_books = mysqli_fetch_assoc($total_books_result)['total_books'] ?? 0;

$total_books_issued_query = "SELECT COUNT(*) AS total_issued FROM transaction WHERE Status = 'Issued'";
$total_books_issued_result = mysqli_query($conn, $total_books_issued_query);
$total_books_issued = mysqli_fetch_assoc($total_books_issued_result)['total_issued'] ?? 0;

$total_fines_query = "SELECT SUM(amount) AS total_fines FROM payments WHERE amount > 0";
$total_fines_result = mysqli_query($conn, $total_fines_query);
$total_fines = mysqli_fetch_assoc($total_fines_result)['total_fines'] ?? 0.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="../assets/js/script.js"></script>
    <style>
        .sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: linear-gradient(135deg, #2c3e50, #1a252f);
    color: white;
    padding: 20px 10px;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    overflow-y: auto;
    transition: width 0.3s ease;
}

/* Sidebar Title */
.sidebar h1 {
    font-size: 24px;
    text-align: center;
    margin-bottom: 30px;
    color: #fff;
    letter-spacing: 1px;
    animation: fadeIn 0.5s ease;
}

/* Navigation Links */
.sidebar nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar nav ul li {
    margin: 10px 0;
}

.sidebar nav ul li a {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    color: #ddd;
    font-size: 16px;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.sidebar nav ul li a i {
    margin-right: 10px;
    font-size: 18px;
    color: #00d1ff;
    transition: transform 0.3s ease;
}

/* Hover Effect */
.sidebar nav ul li a:hover {
    background-color: #007bff;
    color: white;
    transform: translateX(5px);
}

.sidebar nav ul li a:hover i {
    transform: rotate(360deg);
}

/* Active Link Styling */
.sidebar nav ul li a.active {
    background-color: #007bff;
    color: white;
}

/* Dropdown Styling */
.dropdown-toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.dropdown-menu {
    display: none;
    padding-left: 20px;
    margin-top: 5px;
    animation: slideDown 0.3s ease;
}

.dropdown-menu li {
    margin: 5px 0;
}

.dropdown-menu a {
    padding: 8px 15px;
    color: #ddd;
    font-size: 14px;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}

.dropdown-menu a:hover {
    background-color: #007bff;
    color: white;
}

/* JavaScript Toggle for Dropdown */
.show-dropdown {
    display: block;
}

/* Scrollbar Styling */
.sidebar::-webkit-scrollbar {
    width: 8px;
}

.sidebar::-webkit-scrollbar-thumb {
    background-color: #007bff;
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-track {
    background-color: #1a252f;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
        padding: 10px;
    }

    .sidebar h1 {
        font-size: 20px;
    }

    .sidebar nav ul li a {
        font-size: 14px;
        padding: 8px 10px;
    }
}

/* Animation Effects */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
/* Header Styling */
.header {
    background-color: #34495e;
    padding: 15px 20px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    position: fixed;
    top: 0;
    right: 0; /* Align header to the right */
    left: 250px; /* Ensure header starts after the sidebar */
    width: calc(100% - 250px); /* Adjust width to fit the remaining space */
    z-index: 100; /* Ensure header stays on top */
}

/* Header Right Section Styling */
.header-right {
    display: flex;
    align-items: center;
}

.profile {
    display: flex;
    align-items: center;
    margin-right: 30px;
    cursor: pointer;
    color: white;
}

.profile img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.profile span {
    font-size: 18px;
    font-weight: bold;
    color: white;
}

.logout-btn {
    background-color: #e74c3c;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.logout-btn:hover {
    background-color: #c0392b;
}

/* Report Section Styling */
.report-section {
    margin-left: 270px; /* This ensures content starts after the sidebar */
    margin-top: 80px;   /* Adjust to avoid overlap with header */
    padding: 30px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    background-color: #f4f4f4;
}


/* Container for Boxes */
.report-container {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
    gap: 20px;
}

.report-box {
    background-color: #fff;
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    min-width: 220px; /* Ensure boxes have a minimum width */
    height: 220px; /* Make the boxes square-shaped */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center; /* Center content */
    position: relative;
    flex: 1; /* Allow boxes to stretch */
    box-sizing: border-box;
}

/* Hover Effect */
.report-box:hover {
    transform: translateY(-8px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.report-icon {
    font-size: 36px;
    color: #3498db;
    margin-bottom: 20px;
    transition: color 0.3s ease;
}

.report-value {
    font-size: 28px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
}

.report-title {
    font-size: 16px;
    color: #7d7d7d;
    text-transform: uppercase;
}

/* Hover Effect for Icons */
.report-box:hover .report-icon {
    color: #007bff; /* Change icon color on hover */
}

/* Responsive Design for Report Section */
@media (max-width: 1200px) {
    .report-box {
        min-height: 180px; /* Ensure content doesn't overflow */
        height: 180px; /* Adjust for smaller screens */
    }
}

@media (max-width: 768px) {
    .report-section {
        padding: 20px; /* Reduce padding on smaller screens */
        flex-direction: column; /* Stack vertically on smaller screens */
        align-items: center;
    }

    .report-box {
        min-height: 150px;
        height: 150px; /* Adjust box height for smaller screens */
        width: 100%; /* Make each box full width on small screens */
        margin-bottom: 15px;
    }

    .report-icon {
        font-size: 30px; /* Slightly smaller icons */
    }

    .report-value {
        font-size: 24px; /* Smaller value font */
    }

    .report-title {
        font-size: 14px; /* Smaller title font */
    }
}

    </style>
</head>
<body>
  
    <aside class="sidebar">
        <h1>Librarian Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="add_books.php"><i class="fas fa-book"></i> Add Book</a></li>
                <li><a href="manage_books.php"><i class="fas fa-book"></i> Manage Book</a></li>
                <li><a href="issuebook.php"><i class="fas fa-book"></i> Issue Book</a></li>
                <li><a href="manage_student.php"><i class="fa-solid fa-users"></i> Manage Student</a></li>
                <li class="dropdown">
                    <a href="#" onclick="toggleDropdown()" class="dropdown-toggle"><i class="fas fa-cog"></i> Settings <i class="fa fa-chevron-down" style=" margin-left: 100px;"></i></a>
                    <ul class="dropdown-menu" id="settingsDropdown">
                        <li><a href="../auth/changepassword.php"><i class="fas fa-key"></i> Change Password</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </aside>

    <header class="header">
        <div class="header-right">
            <div class="profile" onclick="openProfileModal()">
                <img src="<?php echo $profilePic; ?>" alt="Profile">
                <span><?php echo htmlspecialchars($fullname); ?></span>
            </div>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="report-section">
        <div class="report-container">
            <div class="report-box">
                <div class="report-icon"><i class="fa-solid fa-users"></i></div>
                <div class="report-value"><?php echo $total_students;; ?></div>
                <div class="report-title">Reg Students</div>
            </div>
            <div class="report-box">
                <div class="report-icon"><i class="fas fa-book"></i></div>
                <div class="report-value"><?php echo $total_books; ?></div>
                <div class="report-title">Books Listed</div>
            </div>
            <div class="report-box">
                <div class="report-icon"><i class="fa-solid fa-bars"></i></div>
                <div class="report-value"><?php echo $total_books_issued; ?></div>
                <div class="report-title">Issued Books</div>
            </div>
            <div class="report-box">
                <div class="report-icon"><i class="fa-solid fa-money-bill"></i></div>
                <div class="report-value">Rs. <?php echo number_format($total_fines, 2); ?></div>
                <div class="report-title">collected Fines</div>
            </div>
        </div>
    </div>

    <div id="profileModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeProfileModal()">Close</button>
            <h2>Your Profile Details</h2>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user_details['fullname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user_details['phone']); ?></p>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_details['id']); ?></p>
            <p><strong>Password:</strong> <?php echo htmlspecialchars($user_details['password']); ?></p>
        </div>
    </div>
</body>
</html>
