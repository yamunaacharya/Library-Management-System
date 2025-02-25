<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id']; 
$fullname = $_SESSION['fullname'];
$profilePic = "../assets/images/profile.jpg"; 

// Fetching user details from the database
$query = "SELECT * FROM users WHERE id = '$user_id' AND role = 'student'";
$result = mysqli_query($conn, $query);
$user_details = mysqli_fetch_assoc($result);

if (!$user_details) {
    echo "<script>alert('Error fetching user details');</script>";
    exit;
}

// Fetching report data
$total_issued_books_query = "SELECT COUNT(*) AS total_issued FROM transaction WHERE S_email = '{$user_details['email']}' 
                             AND Status IN ('Issued', 'Returned')";
$total_issued_books_result = mysqli_query($conn, $total_issued_books_query);
$total_issued_books = mysqli_fetch_assoc($total_issued_books_result)['total_issued'] ?? 0;


$books_not_returned_query = "SELECT COUNT(*) AS not_returned FROM transaction WHERE S_email = '{$user_details['email']}' AND Status = 'Issued' AND Due_date < CURDATE()";
$books_not_returned_result = mysqli_query($conn, $books_not_returned_query);
$books_not_returned = mysqli_fetch_assoc($books_not_returned_result)['not_returned'] ?? 0;

$total_fines_query = "SELECT SUM(Fine) AS total_fine FROM transaction WHERE S_email = '{$user_details['email']}' AND Fine > 0 AND Status = 'Issued'";
$total_fines_result = mysqli_query($conn, $total_fines_query);
$total_fines = mysqli_fetch_assoc($total_fines_result)['total_fine'] ?? 0.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../librarian/style.css">
    <link rel="stylesheet" href="style.css">
    <script src="../assets/js/script.js"></script>
    <style>
        body {
    font-family: Arial, sans-serif; /* Sets the font to Arial, with a fallback to sans-serif */
    margin: 0; /* Removes default margin from the body */
    padding: 0; /* Removes default padding from the body */
    display: flex; /* Uses Flexbox for layout */
    flex-direction: column; /* Arranges children elements in a column (vertical layout) */
    min-height: 100vh; /* Ensures the body takes at least the full height of the viewport */
    background-color: #f4f7fc; /* Sets a light grey-blue background color */
}
/* Sidebar Styling */
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
    }

    .sidebar h1 {
        font-size: 20px;
    }

    .sidebar nav ul li a {
        font-size: 14px;
        padding: 8px 10px;
    }

    .dropdown-toggle i {
        margin-left: 40px;
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
    width: calc(100% - 250px); /* Adjust width to exclude sidebar */
    height: 70px;
    background: #34495e;
    color: white;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 0 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    position: fixed;
    top: 0;
    left: 250px; /* Align after sidebar */
    z-index: 1000;
}

/* Right Section of Header */
.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* Profile Section */
.profile {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 20px;
    background-color: rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.profile:hover {
    background-color: rgba(0, 123, 255, 0.3);
    transform: translateY(-2px);
}

.profile img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
    border: 2px solid #00d1ff;
    transition: transform 0.3s ease;
}

.profile:hover img {
    transform: scale(1.1);
}

.profile span {
    font-size: 16px;
    color: white;
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* Logout Button */
.logout-btn {
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    font-size: 14px;
    text-decoration: none;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    background-color: #ff4d4d;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 0 10px rgba(255, 77, 77, 0.8);
}

/* Container for Report Boxes */
.report-container {
    display: flex; /* Uses Flexbox for layout */
    justify-content: space-around; /* Distributes report boxes evenly with space between them */
    gap: 20px; /* Adds a gap between each report box */
    flex-wrap: wrap; /* Allows items to wrap onto new lines if the screen is too narrow */
}

/* Report Box Styling */
.report-box {
    background-color: #fff; /* Sets the background color of the report box to white */
    padding: 20px; /* Adds padding inside the report box */
    border-radius: 10px; /* Rounds the corners of the report box */
    width: 220px; /* Sets a fixed width for each report box */
    text-align: center; /* Centers the text inside the report box */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Adds a subtle shadow around the report box */
    transition: transform 0.3s ease; /* Adds a smooth transform effect on hover */
}

/* Hover Effect for Report Box */
.report-box:hover {
    transform: translateY(-10px); /* Moves the report box up slightly when hovered */
}

/* Icon Inside Report Box */
.report-icon {
    font-size: 40px; /* Sets the font size of the icon inside the report box */
    color: #3498db; /* Sets the color of the icon to blue */
    margin-bottom: 15px; /* Adds space below the icon */
}

/* Value Inside Report Box */
.report-value {
    font-size: 24px; /* Sets the font size of the report value */
    font-weight: bold; /* Makes the value text bold */
    color: #2c3e50; /* Sets the text color to a dark grey-blue */
    margin-bottom: 10px; /* Adds space below the value */
}

/* Title Inside Report Box */
.report-title {
    font-size: 16px; /* Sets the font size of the report title */
    color: #7f8c8d; /* Sets the title text color to grey */
}

/* Responsive Design */
@media (max-width: 768px) {
    .header {
        width: calc(100% - 200px);
        left: 200px;
    }

    .profile img {
        width: 35px;
        height: 35px;
    }

    .profile span {
        font-size: 14px;
    }

    .logout-btn {
        padding: 6px 12px;
        font-size: 12px;
    }
}


@media (max-width: 480px) {
    .sidebar {
        width: 100px;
    }
    .header {
        width: calc(100% - 100px);
        left: 100px;
    }

}
/* Modal background */
.modal {
    display: none;  /* Hidden by default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);  /* Semi-transparent black background */
    z-index: 9999;  /* Ensure it's on top of other elements */
}

/* Modal content box */
.modal-content {
    position: relative;
    margin: 10% auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    width: 400px;  /* Adjust the width as needed */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Modal close button */
.modal-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #ff4d4d;
    border: none;
    color: white;
    font-size: 18px;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
}

.modal-close:hover {
    background-color: #ff3333;
}

/* Modal title */
.modal h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

/* Modal paragraph styling */
.modal p {
    font-size: 16px;
    margin: 8px 0;
    color: #555;
}

.modal p strong {
    color: #333;
}

@media (max-width: 480px) {
    .modal-content {
        width: 90%;  /* Make the modal content responsive for smaller screens */
    }
}

    </style>
    
</head>
<body>
    <aside class="sidebar">
        <h1>Student Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="requestbook.php"><i class="fa-solid fa-book"></i> Request Book</a></li>
                <li><a href="issuedbooks.php"><i class="fa-solid fa-book"></i> Issued Books</a></li>
                <li><a href="returnbooks.php"><i class="fa-solid fa-book"></i> Return Books</a></li>
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
                <div class="report-icon"><i class="fas fa-book"></i></div>
                <div class="report-value"><?php echo $total_issued_books; ?></div>
                <div class="report-title">Total Issued Books</div>
            </div>
            <div class="report-box">
                <div class="report-icon"><i class="fas fa-recycle"></i></div>
                <div class="report-value"><?php echo $books_not_returned; ?></div>
                <div class="report-title">Books Not Returned</div>
            </div>
            <div class="report-box">
                <div class="report-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="report-value">Rs. <?php echo number_format($total_fines, 2); ?></div>
                <div class="report-title">Total Due Fines</div>
            </div>
        </div>
    </div>

    <!-- Fetching user details from the database -->
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
