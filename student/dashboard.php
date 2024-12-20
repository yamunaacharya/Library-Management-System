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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../librarian/style.css">
</head>
<body>
  
    <aside class="sidebar">
        <h1>Student Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="requestbook.php"><i class="fa-solid fa-book"></i> Request Book</a></li>
                <li><a href="issuedbooks.php"><i class="fa-solid fa-book"></i> Issued Books</a></li>
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
 
    <!-- fetching users details from the database  -->
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

    <script>
        // to Open Profile Modal
        function openProfileModal() {
            document.getElementById('profileModal').style.display = 'flex';
        }

        // to Close Profile Modal
        function closeProfileModal() {
            document.getElementById('profileModal').style.display = 'none';
        }
    </script>

</body>
</html>
