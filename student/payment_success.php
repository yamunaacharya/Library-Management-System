<?php
require 'includes/config.php';

$amount = htmlspecialchars($_GET['amount'] ?? '0');
$status = htmlspecialchars($_GET['status'] ?? 'Unknown');
$mobile = htmlspecialchars($_GET['mobile'] ?? 'N/A');
$orderId = htmlspecialchars($_GET['purchase_order_id'] ?? 'N/A');
$orderName = htmlspecialchars($_GET['purchase_order_name'] ?? 'N/A');

if ($orderId !== 'N/A') {
    try {
        $updateQuery = "UPDATE payments SET status = ?, amount = ? WHERE purchase_order_id = ?";
        $stmt = $conn->prepare($updateQuery);

        if ($stmt) {
            $stmt->bind_param("sds", $status, $amount, $orderId);

            if (!$stmt->execute()) {
                echo "<script>alert('Failed to update payment status in the database. Please contact support.');</script>";
            } else {
            }
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare the database statement.");
        }
    } catch (Exception $e) {
        error_log("Payment update error: " . $e->getMessage());
        echo "<script>alert('An error occurred while processing your payment. Please contact support.');</script>";
    }
} else {
    echo "<script>alert('Invalid payment details received. Please contact support.');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .success-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .success-container h1 {
            color: purple;
            font-size: 2em;
            margin-bottom: 10px;
        }
        .success-container p {
            color: #555;
            margin: 5px 0;
            line-height: 1.5;
        }
        .success-container .highlight {
            font-weight: bold;
            color: #333;
        }
        .success-container .details {
            margin-top: 15px;
            text-align: left;
            font-size: 0.9em;
            color: #444;
        }
        .success-container a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: purple;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .success-container a:hover {
            background-color: violet;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h1>Payment Successful</h1>
        <p>Thank you for your payment!</p>
        <div class="details">
            <p><span class="highlight">Order ID:</span> <?php echo $orderId; ?></p>
            <p><span class="highlight">Order Name:</span> <?php echo $orderName; ?></p>
            <p><span class="highlight">Amount Paid:</span> NPR <?php echo $amount; ?></p>
            <p><span class="highlight">Mobile:</span> <?php echo $mobile; ?></p>
            <p><span class="highlight">Status:</span> <?php echo $status; ?></p>
        </div>
        <a href="dashboard.php">Go to Dashboard</a>
    </div>
</body>
</html>
