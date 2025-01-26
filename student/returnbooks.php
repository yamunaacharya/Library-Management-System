<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}

$student_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $student_email; 
    $title = mysqli_real_escape_string($conn, $_POST['title']);

    $query = "SELECT 
                  t.T_id, t.Due_date, t.Fine, t.Status, 
                  b.B_id, b.Quantity, b.Status AS Book_Status 
              FROM transaction t
              JOIN books b ON t.B_id = b.B_id
              WHERE t.S_email = ? AND b.Title = ? AND t.Status = 'Issued'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $transaction_id = $row['T_id'];
        $book_id = $row['B_id'];
        $due_date = $row['Due_date'];
        $current_date = date("Y-m-d");

        if ($current_date <= $due_date) {
            $return_query = "UPDATE transaction SET Return_date = ?, Status = 'Returned' WHERE T_id = ?";
            $stmt = $conn->prepare($return_query);
            $stmt->bind_param("si", $current_date, $transaction_id);

            if ($stmt->execute()) {
                $update_quantity_query = "UPDATE books SET Quantity = Quantity + 1, Status = 'Available' WHERE B_id = ?";
                $update_stmt = $conn->prepare($update_quantity_query);
                $update_stmt->bind_param("i", $book_id);
                $update_stmt->execute();

                echo "<script>alert('Book returned successfully!'); window.location.href='dashboard.php';</script>";
            } else {
                echo "<script>alert('Error processing the return. Please try again.');</script>";
            }
        } else {
            $late_days = (strtotime($current_date) - strtotime($due_date)) / (60 * 60 * 24);
            $fine = $late_days * 3;

            $fine_query = "UPDATE transaction SET Fine = ? WHERE T_id = ?";
            $stmt = $conn->prepare($fine_query);
            $stmt->bind_param("di", $fine, $transaction_id);
            $stmt->execute();

            $_SESSION['transaction_id'] = $transaction_id;
            $_SESSION['fine_amount'] = $fine;
            echo "<script>
                alert('You are $late_days days late. You have accrued a fine of $fine. Please proceed to pay the fine.');
                window.location.href = 'pay_fine.php';
            </script>";
            exit;
        }
    } else {
        echo "<script>alert('You haven\'t issued this book.');</script>";
    }
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
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>
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
    <main class="main-content">
        <header class="dashboard-header">
            <h3 class="text-center">Return Issued Book</h3><br>
        </header>
        <div class="container my-5">
            <form method="POST" action="">
                <label for="email">Your Email:</label><br>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student_email); ?>" readonly><br><br>

                <?php
                // Fetch the list of issued books for the student
                $issued_books_query = "SELECT b.Title FROM transaction t JOIN books b ON t.B_id = b.B_id WHERE t.S_email = ? AND t.Status = 'Issued'";
                $stmt = $conn->prepare($issued_books_query);
                $stmt->bind_param("s", $student_email);
                $stmt->execute();
                $issued_books_result = $stmt->get_result();
                ?>

                <label for="title">Book Title:</label><br>
                <select id="title" name="title" required>
                    <option value="" disabled selected>Select a book</option>
                    <?php while ($book = $issued_books_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($book['Title']); ?>"><?php echo htmlspecialchars($book['Title']); ?></option>
                    <?php endwhile; ?>
                </select><br><br>

                <button type="submit" class=" btn-primary">Return Book</button>
            </form>
        </div>
    </main>
</body>
</html>
