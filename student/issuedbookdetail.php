<?php
require '../includes/config.php';

// Start the session to get the student email
session_start();

// Check if the session contains the student email
if (!isset($_SESSION['student_email'])) {
    echo "You must log in first!";
    exit;
}

// Get the student email from the session
$student_email = $_SESSION['student_email'];

// Query to fetch borrowed books for the student
$sql = "SELECT b.b_id, b.title, b.ISBN, b.authors, t.issue_date, t.due_date, t.return_date, t.fine, t.status
        FROM boooks b
        JOIN transactions t ON b.b_id = t.b_id 
        WHERE t.s_email = '$student_email' AND (t.status = 'borrowed' OR t.status = 'requested' OR t.status = 'returned')";

// Execute the query
$result = mysqli_query($conn, $sql);

// Debugging: Print the query if no books are found
if (!$result) {
    echo "Error in query: " . mysqli_error($conn);
    exit;
}

// Check if any books were found
if (mysqli_num_rows($result) > 0) {
    echo "<h3>Your Issued Books Details</h3>";
    echo "<table class='table-border'>";
    echo "<tr>
            <th>Book ID</th>
            <th>Title</th>
            <th>ISBN</th>
            <th>Authors</th>
            <th>Issue Date</th>
            <th>Due Date</th>
            <th>Return Date</th>
            <th>Fine</th>
            <th>Status</th>
        </tr>";
    echo "<tbody>";

    // Fetch each borrowed book
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['b_id']}</td>
                <td>{$row['title']}</td>
                <td>{$row['ISBN']}</td>
                <td>{$row['authors']}</td>
                <td>{$row['issue_date']}</td>
                <td>{$row['due_date']}</td>";
        
        $return_date = $row['return_date'] ? $row['return_date'] : 'Not Returned Yet';
        echo "<td>$return_date</td>";
        
        $fine = $row['fine'] ? '$' . number_format($row['fine'], 2) : 'No Fine';
        echo "<td>$fine</td>";
        
        echo "<td>{$row['status']}</td>
              </tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>You have not borrowed any books yet.</p>";
}
?>
