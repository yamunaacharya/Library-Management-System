<?php
require '../includes/config.php';

// Function to check if a student can issue a book
function canIssueBook($conn, $student_email, $book_id) {
    // Check if the student has already issued this book
    $check_same_book_sql = "SELECT * FROM transaction 
                            WHERE s_email = ? AND b_id = ? AND status = 'borrowed'";
    $stmt = $conn->prepare($check_same_book_sql);
    $stmt->bind_param("si", $student_email, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Error: You have already borrowed this book.<br>";
        return false;
    }

    // Check if the student has already borrowed 5 different books
    $check_limit_sql = "SELECT COUNT(*) AS book_count FROM transaction 
                        WHERE s_email = ? AND status = 'borrowed'";
    $stmt = $conn->prepare($check_limit_sql);
    $stmt->bind_param("s", $student_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['book_count'] >= 5) {
        echo "Error: You cannot borrow more than 5 books at a time.<br>";
        return false;
    }

    return true;
}

// Function to issue a book
function issueBook($conn, $student_email, $book_title) {
    // Verify student exists and role is 'student'
    $student_sql = "SELECT email FROM usersss WHERE email = ? AND role = 'student'";
    $stmt = $conn->prepare($student_sql);
    $stmt->bind_param("s", $student_email);
    $stmt->execute();
    $student_result = $stmt->get_result();
    if ($student_result->num_rows === 0) {
        echo "Error: Student not found or invalid role.<br>";
        return;
    }

    // Verify book exists and is available
    $book_sql = "SELECT b_id, quantity FROM boooks WHERE title = ? AND status = 'Available'";
    $stmt = $conn->prepare($book_sql);
    $stmt->bind_param("s", $book_title);
    $stmt->execute();
    $book_result = $stmt->get_result();

    if ($book_result->num_rows === 0) {
        echo "Error: Book not found or not available.<br>";
        return;
    }
    $book = $book_result->fetch_assoc();
    $book_id = $book['b_id'];
    $quantity = $book['quantity'];

    if ($quantity <= 0) {
        echo "Error: No copies left to issue.<br>";
        return;
    }

    // Check if student can issue the book
    if (!canIssueBook($conn, $student_email, $book_id)) {
        return;
    }

    // Set issue and due dates
    $issue_date = date("Y-m-d");
    $due_date = date("Y-m-d", strtotime("+14 days")); // Due date 14 days from today

    // Insert into transaction table
    $insert_sql = "INSERT INTO transaction (s_email, b_id, issue_date, due_date, status) 
                   VALUES (?, ?, ?, ?, 'borrowed')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("siss", $student_email, $book_id, $issue_date, $due_date);

    if ($stmt->execute()) {
        // Update book status and quantity
        $update_sql = "UPDATE boooks SET quantity = quantity - 1, 
                       status = CASE WHEN quantity = 1 THEN 'Borrowed' ELSE 'Available' END 
                       WHERE b_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();

        echo "Book issued successfully!<br>";
        echo "Issue Date: $issue_date <br>";
        echo "Due Date: $due_date <br>";
    } else {
        echo "Error: Unable to issue the book.";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_email = $_POST['student_email'];
    $book_title = $_POST['book_title'];

    if (!empty($student_email) && !empty($book_title)) {
        issueBook($conn, $student_email, $book_title);
    } else {
        echo "Please provide both student email and book title.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Book</title>
</head>
<body>
    <h2>Issue Book</h2>
    <form method="POST" action="">
        <label for="student_email">Student Email:</label><br>
        <input type="email" id="student_email" name="student_email" required><br><br>

        <label for="book_title">Book Title:</label><br>
        <input type="text" id="book_title" name="book_title" required><br><br>

        <input type="submit" value="Issue Book">
    </form>
</body>
</html>
