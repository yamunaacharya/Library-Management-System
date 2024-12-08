<?php
require '../includes/config.php';

// Fetch all pending requests
function fetchPendingRequests() {
    global $conn;
    $stmt = $conn->prepare("SELECT t.t_id, t.s_email, b.title, t.issue_date FROM transactions t INNER JOIN boooks b ON t.b_id = b.b_id WHERE t.status = 'requested'");
    $stmt->execute();
    return $stmt->get_result();
}

// Handle request approval or rejection
function handleRequest($transactionId, $action, $newDueDate = null) {
    global $conn;

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE transactions SET status = 'borrowed', due_date = ? WHERE t_id = ?");
        $stmt->bind_param('si', $newDueDate, $transactionId);
    } else {
        $stmt = $conn->prepare("UPDATE transactions SET status = 'rejected' WHERE t_id = ?");
        $stmt->bind_param('i', $transactionId);
    }

    if ($stmt->execute()) {
        echo $action === 'approve' ? "Request approved. The book is borrowed.<br>" : "Request rejected.<br>";
    } else {
        echo "Failed to process the request.<br>";
    }
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $transactionId = $_POST['transactionId'];

    if (isset($_POST['approve'])) {
        $newDueDate = $_POST['dueDate'];
        handleRequest($transactionId, 'approve', $newDueDate);
    } elseif (isset($_POST['reject'])) {
        handleRequest($transactionId, 'reject');
    }
}

// Fetch pending requests
$pendingRequests = fetchPendingRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Book Requests</title>
</head>
<body>
    <h2>Manage Book Requests</h2>

    <table>
            <tr>
                <th>Transaction ID</th>
                <th>Student Email</th>
                <th>Book Title</th>
                <th>Request Date</th>
                <th>Action</th>
            </tr>
        <tbody>
            <?php while ($row = $pendingRequests->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['t_id']; ?></td>
                    <td><?php echo $row['s_email']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['issue_date']; ?></td>
                    <td>
                        <!-- Approve Form -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="transactionId" value="<?php echo $row['t_id']; ?>">
                            <label for="dueDate_<?php echo $row['t_id']; ?>">Due Date:</label>
                            <input type="date" id="dueDate_<?php echo $row['t_id']; ?>" name="dueDate" required>
                            <button type="submit" name="approve">Approve</button>
                        </form>

                        <!-- Reject Form -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="transactionId" value="<?php echo $row['t_id']; ?>">
                            <button type="submit" name="reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
