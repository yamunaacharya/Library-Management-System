<?php
require '../includes/config.php';

session_start();

if (isset($_SESSION['user_id']) || isset($_SESSION['email'])) {
    $_SESSION = [];
    session_destroy();
}

if (isset($conn)) {
    $conn->close();
}

header("Location: ../auth/index.php");
exit;
?>

