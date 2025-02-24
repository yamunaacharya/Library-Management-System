<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['transaction_id']) || !isset($_SESSION['fine_amount'])) {
    header("Location: dashboard.php");
    exit;
}

$transaction_id = $_SESSION['transaction_id'];
$fine_amount = $_SESSION['fine_amount'];
$student_email = $_SESSION['email'];

// Get user details
$user_query = "SELECT fullname FROM users WHERE email = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $student_email);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate unique payment ID
    $purchase_order_id = uniqid('LIB');
    
    // Insert payment record
    $insert_payment = "INSERT INTO payments (purchase_order_id, email, amount, status) 
                      VALUES (?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($insert_payment);
    $stmt->bind_param("ssd", $purchase_order_id, $student_email, $fine_amount);
    
    if (!$stmt->execute()) {
        echo "<script>alert('Error initializing payment. Please try again.');</script>";
        exit;
    }

    // Update transaction with payment reference
    $update_trans = "UPDATE transaction SET payment_id = ? WHERE T_id = ?";
    $stmt = $conn->prepare($update_trans);
    $stmt->bind_param("si", $purchase_order_id, $transaction_id);
    $stmt->execute();

    // Khalti API request
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/initiate/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(array(
            "return_url" => "http://localhost/library/student/payment_success.php",
            "website_url" => "http://localhost/library",
            "amount" => $fine_amount * 100,
            "purchase_order_id" => $purchase_order_id,
            "purchase_order_name" => "Library Fine Payment",
            "customer_info" => array(
                "name" => $user['fullname'],
                "email" => $student_email
            )
        )),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Key e2ac8b6feae440a1838ee5d06d4cc05e',
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    
    $response_data = json_decode($response, true);
    
    if (isset($response_data['payment_url'])) {
        header('Location: ' . $response_data['payment_url']);
        exit;
    } else {
        echo "<script>alert('Payment initialization failed. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Fine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <pre>
        <h2>Fine Payment</h2>
        <p>Amount to Pay: NPR <?php echo $fine_amount; ?></p>
        <form method="POST">
            <button type="submit" class="btn-primary">Proceed to Payment</button>
        </form>
        <a href="dashboard.php" class="btn">Cancel</a>
        </pre>
    </div>
</body>
</html>
