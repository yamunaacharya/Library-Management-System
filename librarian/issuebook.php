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

