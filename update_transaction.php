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

// Fetch transaction details
$sql = "SELECT * FROM transactions WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

// Check if form is submitted for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_number = $_POST['phone_number'];
    $amount = $_POST['amount'];
    $product_name = $_POST['product_name'];
    $transaction_date = $_POST['transaction_date'];
    $mpesa_receipt_number = $_POST['mpesa_receipt_number'];
    $result_code = $_POST['result_code'];
    $result_desc = $_POST['result_desc'];
    $status = $_POST['status'];

    $update_sql = "UPDATE transactions SET phone_number = ?, amount = ?, product_name = ?, transaction_date = ?, mpesa_receipt_number = ?, result_code = ?, result_desc = ?, status = ? WHERE id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param("sdsssisii", $phone_number, $amount, $product_name, $transaction_date, $mpesa_receipt_number, $result_code, $result_desc, $status, $id);

    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Transaction updated successfully!";
        header('Location: transactions.php');
        exit();
    } else {
        echo "Error: " . $con->error;
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Transaction</title>
    <link rel="stylesheet" href="transactions.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Update Transaction</h1>
        </header>
        <form action="" method="post">
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($transaction['phone_number']); ?>" required><br>
            <label for="amount">Amount:</label>
            <input type="number" step="0.01" name="amount" id="amount" value="<?php echo htmlspecialchars($transaction['amount']); ?>" required><br>
            <label for="product_name">Product Name:</label>
            <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($transaction['product_name']); ?>" required><br>
            <label for="transaction_date">Transaction Date:</label>
            <input type="datetime-local" name="transaction_date" id="transaction_date" value="<?php echo date('Y-m-d\TH:i', strtotime($transaction['transaction_date'])); ?>" required><br>
            <label for="mpesa_receipt_number">MPESA Receipt Number:</label>
            <input type="text" name="mpesa_receipt_number" id="mpesa_receipt_number" value="<?php echo htmlspecialchars($transaction['mpesa_receipt_number']); ?>"><br>
            <label for="result_code">Result Code:</label>
            <input type="number" name="result_code" id="result_code" value="<?php echo htmlspecialchars($transaction['result_code']); ?>"><br>
            <label for="result_desc">Result Description:</label>
            <input type="text" name="result_desc" id="result_desc" value="<?php echo htmlspecialchars($transaction['result_desc']); ?>"><br>
            <label for="status">Status:</label>
            <input type="text" name="status" id="status" value="<?php echo htmlspecialchars($transaction['status']); ?>" required><br>
            <button type="submit">Update Transaction</button>
        </form>
    </div>
</body>
</html>
