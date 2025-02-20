<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = intval($_POST['transaction_id']);
    $action = $_POST['action']; 

    if ($action === 'accept') {
        $fetch_query = "SELECT B_id FROM transaction WHERE T_id = $transaction_id";
        $fetch_result = mysqli_query($conn, $fetch_query);
        $row = mysqli_fetch_assoc($fetch_result);
        $book_id = $row['B_id'];

        $quantity_check_query = "SELECT Quantity FROM books WHERE B_id = $book_id";
        $quantity_check_result = mysqli_query($conn, $quantity_check_query);
        $quantity_row = mysqli_fetch_assoc($quantity_check_result);

        if ($quantity_row['Quantity'] > 0) {
            $issue_date = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime('+14 days')); 
            $update_transaction_query = "UPDATE transaction 
                                         SET Status = 'Issued', Issue_date = '$issue_date', Due_date = '$due_date' 
                                         WHERE T_id = $transaction_id";

            $update_quantity_query = "UPDATE books SET Quantity = Quantity - 1 WHERE B_id = $book_id";

            if (mysqli_query($conn, $update_transaction_query) && mysqli_query($conn, $update_quantity_query)) {
                echo "<script>alert('Book issued successfully!'); window.location.href = 'issuebook.php';</script>";
            } else {
                echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('The requested book is out of stock.'); window.history.back();</script>";
        }
    } elseif ($action === 'reject') {
        $update_query = "UPDATE transaction SET Status = 'Rejected' WHERE T_id = $transaction_id";

        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Request rejected successfully.'); window.location.href = 'issuebook.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
        }
    }
}

$fetch_query = "SELECT t.T_id, t.S_email, b.Title, t.Status 
                FROM transaction t 
                INNER JOIN books b ON t.B_id = b.B_id 
                WHERE t.Status = 'Requested'";
$fetch_result = mysqli_query($conn, $fetch_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
/* Main Content Styling */
.main-content {
    margin-left: 260px; /* Sidebar width adjustment */
    padding: 20px;
    background-color: #f9f9f9;
}

/* Header Styling */
.dashboard-header {
    background: #2c3e50;
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}

.dashboard-header h3 {
    font-size: 28px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #f4f4f4;
}

/* Container Styling */
.container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Table Styling */
.table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #2c3e50;
    color: white;
    font-size: 16px;
}

.table tr:nth-child(even) {
    background-color: #f4f4f4;
}

/* Buttons */
.btn {
    padding: 8px 15px;
    font-size: 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

/* Center text when no requests */
.text-center {
    font-size: 18px;
    color: #555;
    margin-top: 30px;
    font-style: italic;
}

/* Form styling */
form {
    display: inline-block;
    margin-right: 10px;
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
    <main class="main-content">
        <header class="dashboard-header">
            <h3 class="text-center">Manage Book Requests</h3><br>
        </header>
        <div class="container my-5">
            <?php if (mysqli_num_rows($fetch_result) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.N ID</th>
                            <th>Student Email</th>
                            <th>Book Title</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($fetch_result)): ?>
                            <tr>
                                <td><?php echo $row['T_id']; ?></td>
                                <td><?php echo $row['S_email']; ?></td>
                                <td><?php echo $row['Title']; ?></td>
                                <td><?php echo $row['Status']; ?></td>
                                <td>
                                    <form action="" method="post" style="display: inline;">
                                        <input type="hidden" name="transaction_id" value="<?php echo $row['T_id']; ?>">
                                        <button type="submit" name="action" value="accept" class="btn btn-success">Accept</button>
                                    </form>
                                    <form action="" method="post" style="display: inline;">
                                        <input type="hidden" name="transaction_id" value="<?php echo $row['T_id']; ?>">
                                        <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No pending book requests at the moment.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>

