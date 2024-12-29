<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo "<script>alert('Session expired or invalid. Please log in again.'); window.location.href = '../auth/login.php';</script>";
    exit;
}

$s_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $book_title = mysqli_real_escape_string($conn, $_POST['book_title']); 

    if ($email !== $s_email) {
        echo "<script>alert('The entered email does not match your registered email.'); window.history.back();</script>";
        exit;
    }

    // Check if the book exists and get its ID and quantity
    $book_query = "SELECT B_id, Quantity FROM books WHERE Title = '$book_title' LIMIT 1";
    $book_result = mysqli_query($conn, $book_query);

    if (mysqli_num_rows($book_result) === 0) {
        echo "<script>alert('Sorry, this book is not available in the library.'); window.history.back();</script>";
        exit;
    }

    $book_data = mysqli_fetch_assoc($book_result);
    $book_id = $book_data['B_id'];
    $quantity = $book_data['Quantity'];

    // Check if the book is already requested or issued
    $check_query = "SELECT * FROM transaction WHERE B_id = $book_id AND Status IN ('Requested', 'Issued')";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) >= $quantity) {
        echo "<script>alert('Sorry, this book is currently unavailable as all copies are either requested or issued.'); window.history.back();</script>";
        exit;
    }

    // Check if the student already has a pending request for this book
    $student_check_query = "SELECT * FROM transaction WHERE S_email = '$s_email' AND B_id = $book_id AND Status = 'Requested'";
    $student_check_result = mysqli_query($conn, $student_check_query);

    if (mysqli_num_rows($student_check_result) > 0) {
        echo "<script>alert('You have already requested this book.'); window.history.back();</script>";
        exit;
    }

    // Check if the student has reached the limit of 5 requested books
    $count_query = "SELECT COUNT(*) AS book_count FROM transaction WHERE S_email = '$s_email' AND Status = 'Requested'";
    $count_result = mysqli_query($conn, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);

    if ($count_row['book_count'] >= 5) {
        echo "<script>alert('You can only request up to 5 books.'); window.history.back();</script>";
        exit;
    }

    // Add the request to the transaction table
    $status = 'Requested';
    $issue_date = NULL; 
    $due_date = NULL;
    $return_date = NULL;
    $fine = 0.00;

    $insert_query = "INSERT INTO transaction (S_email, B_id, Issue_date, Due_date, Return_date, Fine, Status) 
                     VALUES ('$s_email', $book_id, '$issue_date', '$due_date', '$return_date', $fine, '$status')";

    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Book request submitted successfully!'); window.location.href = 'requestbook.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
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
            <h3 class="text-center">Request a Book</h3><br>
        </header>
        <div class="container my-5">
            <!-- Form to request a book -->
            <form action="" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your registered email" value="<?php echo $s_email; ?>" required>
                </div>
                <div class="form-group">
                    <label for="book_title">Book Title</label>
                    <input type="text" class="form-control" name="book_title" id="book_title" placeholder="Enter Book Title" required>
                </div>
                <button type="submit" class="btn-primary" name="submit">Request Book</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>