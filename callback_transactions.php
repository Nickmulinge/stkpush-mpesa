<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "stkpush";

$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if action is delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM callback_responses WHERE id = $id";
    if ($con->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $con->error;
    }
}

// Check if action is update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $transaction_id = $_POST['transaction_id'];
    $merchant_request_id = $_POST['merchant_request_id'];
    $checkout_request_id = $_POST['checkout_request_id'];
    $result_code = $_POST['result_code'];
    $result_desc = $_POST['result_desc'];
    $mpesa_receipt_number = $_POST['mpesa_receipt_number'];
    $balance = $_POST['balance'];
    $transaction_date = $_POST['transaction_date'];

    $sql = "UPDATE callback_responses SET transaction_id='$transaction_id', merchant_request_id='$merchant_request_id', checkout_request_id='$checkout_request_id', result_code='$result_code', result_desc='$result_desc', mpesa_receipt_number='$mpesa_receipt_number', balance='$balance', transaction_date='$transaction_date' WHERE id = $id";
    if ($con->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $con->error;
    }
}

// Fetch callback responses
$sql = "SELECT * FROM callback_responses";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Callback Transactions</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .update-form {
            display: inline-block;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        footer {
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Callback Transactions</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Transaction ID</th>
                <th>Merchant Request ID</th>
                <th>Checkout Request ID</th>
                <th>Result Code</th>
                <th>Result Description</th>
                <th>Mpesa Receipt Number</th>
                <th>Balance</th>
                <th>Transaction Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['merchant_request_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['checkout_request_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['result_code']); ?></td>
                    <td><?php echo htmlspecialchars($row['result_desc']); ?></td>
                    <td><?php echo htmlspecialchars($row['mpesa_receipt_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['balance']); ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                    <td>
                        <button onclick="showModal('<?php echo $row['id']; ?>', '<?php echo $row['transaction_id']; ?>', '<?php echo $row['merchant_request_id']; ?>', '<?php echo $row['checkout_request_id']; ?>', '<?php echo $row['result_code']; ?>', '<?php echo $row['result_desc']; ?>', '<?php echo $row['mpesa_receipt_number']; ?>', '<?php echo $row['balance']; ?>', '<?php echo $row['transaction_date']; ?>')">Update</button>
                        <a href="?action=delete&id=<?php echo $row['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form action="callback_transactions.php" method="post">
                <input type="hidden" name="id" id="callbackId">
                <label for="transaction_id">Transaction ID:</label>
                <input type="text" name="transaction_id" id="callbackTransactionId" required><br><br>
                <label for="merchant_request_id">Merchant Request ID:</label>
                <input type="text" name="merchant_request_id" id="callbackMerchantRequestId" required><br><br>
                <label for="checkout_request_id">Checkout Request ID:</label>
                <input type="text" name="checkout_request_id" id="callbackCheckoutRequestId" required><br><br>
                <label for="result_code">Result Code:</label>
                <input type="number" name="result_code" id="callbackResultCode" required><br><br>
                <label for="result_desc">Result Description:</label>
                <input type="text" name="result_desc" id="callbackResultDesc" required><br><br>
                <label for="mpesa_receipt_number">Mpesa Receipt Number:</label>
                <input type="text" name="mpesa_receipt_number" id="callbackMpesaReceiptNumber"><br><br>
                <label for="balance">Balance:</label>
                <input type="number" name="balance" id="callbackBalance" step="0.01"><br><br>
                <label for="transaction_date">Transaction Date:</label>
                <input type="datetime-local" name="transaction_date" id="callbackTransactionDate" required><br><br>
                <button type="submit" name="update" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Update</button>
            </form>
        </div>
    </div>

    

    <script>
        function showModal(id, transaction_id, merchant_request_id, checkout_request_id, result_code, result_desc, mpesa_receipt_number, balance, transaction_date) {
            document.getElementById('callbackId').value = id;
            document.getElementById('callbackTransactionId').value = transaction_id;
            document.getElementById('callbackMerchantRequestId').value = merchant_request_id;
            document.getElementById('callbackCheckoutRequestId').value = checkout_request_id;
            document.getElementById('callbackResultCode').value = result_code;
            document.getElementById('callbackResultDesc').value = result_desc;
            document.getElementById('callbackMpesaReceiptNumber').value = mpesa_receipt_number;
            document.getElementById('callbackBalance').value = balance;
            document.getElementById('callbackTransactionDate').value = transaction_date;
            document.getElementById('myModal').style.display = "block";
        }

        // Get the modal
        var modal = document.getElementById('myModal');

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName('close')[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
 
</body>
</html>

<?php
$con->close();
?>
