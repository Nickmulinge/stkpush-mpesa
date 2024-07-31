<?php
// Retrieve the request body sent by Mpesa
$requestBody = file_get_contents('php://input');

// Log the request for debugging purposes
file_put_contents('mpesa_callback.log', $requestBody . PHP_EOL, FILE_APPEND);

// Decode the JSON data
$data = json_decode($requestBody, true);

// Extract relevant information from the callback data
$transactionId = $data['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
$resultCode = $data['Body']['stkCallback']['ResultCode'];
$resultDesc = $data['Body']['stkCallback']['ResultDesc'];

// Update the database with the transaction status
$conn = new mysqli("localhost", "root", "", "stkpush"); // Update with your database credentials
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($resultCode == 0) {
    // Payment was successful
    $updateStmt = $conn->prepare("UPDATE transactions SET status = 'Success', result_code = ?, result_desc = ? WHERE id = ?");
    $updateStmt->bind_param("isi", $resultCode, $resultDesc, $transactionId);
    $updateStmt->execute();
    $updateStmt->close();
} else {
    // Payment failed
    $updateStmt = $conn->prepare("UPDATE transactions SET status = 'Failed', result_code = ?, result_desc = ? WHERE id = ?");
    $updateStmt->bind_param("isi", $resultCode, $resultDesc, $transactionId);
    $updateStmt->execute();
    $updateStmt->close();
}

$conn->close();

// Send a response to Mpesa indicating that the callback was received successfully
echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Callback received successfully']);
?>
