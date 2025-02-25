<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id']; 
$fullname = $_SESSION['fullname'];
$profilePic = "../assets/images/profile.jpg"; 

// Fetching user details from the database
$query = "SELECT * FROM users WHERE id = '$user_id' AND role = 'admin'";
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

$total_librarians_query = "SELECT COUNT(*) AS total_librarians FROM users WHERE role = 'librarian'";
$total_librarians_result = mysqli_query($conn, $total_librarians_query);
$total_librarians = mysqli_fetch_assoc($total_librarians_result)['total_librarians'] ?? 0;

$total_books_query = "SELECT COUNT(*) AS total_books FROM books";
$total_books_result = mysqli_query($conn, $total_books_query);
$total_books = mysqli_fetch_assoc($total_books_result)['total_books'] ?? 0;

$total_fines_query = "SELECT SUM(amount) AS total_fines FROM payments WHERE amount > 0";
$total_fines_result = mysqli_query($conn, $total_fines_query);
$total_fines = mysqli_fetch_assoc($total_fines_result)['total_fines'] ?? 0.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../librarian/style.css">
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

    </style>
    </head>
<body>
  
    <aside class="sidebar">
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="adduser.php"><i class="fas fa-user-plus"></i> Add Librarian</a></li>
                <li><a href="managelibrarian.php"><i class="fa-solid fa-users"></i> Manage Librarian</a></li>
                <li><a href="report.php"><i class="fas fa-chart-line"></i> Reports</a></li>
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
                <div class="report-icon"><i class="fa-solid fa-users"></i></div>
                <div class="report-value"><?php echo $total_librarians;; ?></div>
                <div class="report-title">Reg Librarians</div>
            </div>
            <div class="report-box">
                <div class="report-icon"><i class="fas fa-book"></i></div>
                <div class="report-value"><?php echo $total_books; ?></div>
                <div class="report-title">Books Listed</div>
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
