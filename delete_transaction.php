<?php
session_start();

// Check if the user is already logged in
if (!isset($_SESSION['admin'])) {
    header('Location: ../index.php');
    exit();
}

// Database connection
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "stkpush";

$con = new mysqli($servername, $dbusername, $dbpassword, $database);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Get transaction ID
$id = $_GET['id'];

// Delete transaction
$sql = "DELETE FROM transactions WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Transaction deleted successfully!";
} else {
    $_SESSION['message'] = "Error: " . $con->error;
}

$stmt->close();
$con->close();

header('Location: transactions.php');
exit();
?>
