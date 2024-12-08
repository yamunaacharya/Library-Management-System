<?php
require '../includes/config.php'; 

// Function to request a book
function requestBook($studentEmail, $bookTitle) {
    global $conn;

    // Check if the book exists
    $stmt = $conn->prepare("SELECT b_id, quantity FROM boooks WHERE title = ?");
    $stmt->bind_param('s', $bookTitle);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();

    if (!$book) {
        echo "No book found with the title '$bookTitle'.";
        return;
    }

    // Check if the book is out of stock
    if ($book['quantity'] <= 0) {
        echo "The book is currently out of stock.";
        return;
    }

    // Check if there is already a transaction with 'requested' or 'borrowed' status for the student and book
    $stmt = $conn->prepare("SELECT 1 FROM transactions WHERE s_email = ? AND b_id = ? AND status IN ('requested', 'borrowed')");
    $stmt->bind_param('si', $studentEmail, $book['b_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "You have already requested or borrowed this book.";
        return;
    }

    // Insert a new transaction with status 'requested' and placeholder due date
    $requestDate = date('Y-m-d');
    $placeholderDueDate = '0000-00-00'; // Placeholder due date
    $stmt = $conn->prepare("INSERT INTO transactions (s_email, b_id, issue_date, due_date, return_date, fine, status) 
                            VALUES (?, ?, ?, ?, NULL, 0.00, 'requested')");
    $stmt->bind_param('siss', $studentEmail, $book['b_id'], $requestDate, $placeholderDueDate);
    $stmt->execute();

    echo "Your request has been submitted successfully.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentEmail = $_POST['studentEmail'];
    $bookTitle = $_POST['bookTitle'];
    requestBook($studentEmail, $bookTitle);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request to Borrow a Book</title>
</head>
<body>
    <h2>Request to Borrow a Book</h2>
    <form method="post" action="">
        <label for="studentEmail">Student Email:</label>
        <input type="email" id="studentEmail" name="studentEmail" required><br><br>

        <label for="bookTitle">Book Title:</label>
        <input type="text" id="bookTitle" name="bookTitle" required><br><br>

        <input type="submit" value="Request Book">
    </form>
</body>
</html>
