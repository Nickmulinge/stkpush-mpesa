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

// Fetch transactions
$sql = "SELECT * FROM transactions";
$result = $con->query($sql);

if ($result === false) {
    die("Error: " . $con->error);
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions Management</title>
    <link rel="stylesheet" href="css/transactions.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Transactions Management</h1>
        </header>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phone Number</th>
                    <th>Amount</th>
                    <th>Product Name</th>
                    <th>Transaction Date</th>
                    <th>MPESA Receipt Number</th>
                    <th>Result Code</th>
                    <th>Result Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['mpesa_receipt_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['result_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['result_desc']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <a href="update_transaction.php?id=<?php echo $row['id']; ?>">Update</a>
                                <a href="delete_transaction.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this transaction?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">No transactions found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
