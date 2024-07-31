<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phoneNumber = $_POST['phonenumber'];
    $amount = $_POST['amount'];
    $productName = $_POST['product-name'];

    // Database connection
    $servername = "localhost";
    $username = "root"; // Replace with your database username
    $password = ""; // Replace with your database password
    $dbname = "stkpush";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert transaction into the database
    $stmt = $conn->prepare("INSERT INTO transactions (phone_number, amount, product_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $phoneNumber, $amount, $productName);
    $stmt->execute();
    $transactionId = $stmt->insert_id;
    $stmt->close();

    // Mpesa API credentials and endpoint
    $consumerKey = 'xx0y8DyX1kbTlpVbkHBh55GmP8bEUQQYrcroDGv0H06NKgob';
    $consumerSecret = 'KYgjDuuYDzGv1vuMSlv4jr7AkCanwxnlfpMoRsVvh9o2VMcqFvUS1S6n9NgkSCKe';
    $shortCode = '174379';
    $lipaNaMpesaOnlineURL = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; // Use live URL for production
    $passKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

    // Callback URL
    $callbackURL = 'https://f899-41-90-64-220.ngrok.io/stkpush/callback.php'; // Replace with the actual URL of your callback.php file

    // Get the access token
    $accessToken = getAccessToken($consumerKey, $consumerSecret);
    if ($accessToken) {
        $response = lipaNaMpesaOnline($accessToken, $phoneNumber, $amount, $shortCode, $passKey, $productName, $transactionId, $callbackURL, $conn);
        $responseDecoded = json_decode($response);

        if (isset($responseDecoded->ResponseCode) && $responseDecoded->ResponseCode == "0") {
            echo '<div class="alert alert-success" role="alert">Push notification sent to your phone. Kindly enter your Mpesa PIN to complete the purchase.</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">There was an error processing your request. Please try again.</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Unable to get access token.</div>';
    }

    $conn->close();
}

function getAccessToken($consumerKey, $consumerSecret) {
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'; // Use live URL for production

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials)); //setting a custom header
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $curl_response = curl_exec($curl);
    $result = json_decode($curl_response);
    if (isset($result->access_token)) {
        return $result->access_token;
    } else {
        return null;
    }
}

function lipaNaMpesaOnline($accessToken, $phoneNumber, $amount, $shortCode, $passKey, $productName, $transactionId, $callbackURL, $conn) {
    $timestamp = date('YmdHis');
    $password = base64_encode($shortCode . $passKey . $timestamp);

    // Ensure phone number is formatted correctly
    if (strpos($phoneNumber, '+') === 0) {
        $phoneNumber = substr($phoneNumber, 1);
    } elseif (strpos($phoneNumber, '0') === 0) {
        $phoneNumber = '254' . substr($phoneNumber, 1);
    }

    $curl_post_data = array(
        'BusinessShortCode' => $shortCode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phoneNumber,
        'PartyB' => $shortCode,
        'PhoneNumber' => $phoneNumber,
        'CallBackURL' => $callbackURL, // Set the callback URL here
        'AccountReference' => 'Order' . $transactionId, // You can customize this
        'TransactionDesc' => 'Payment for ' . $productName
    );

    $data_string = json_encode($curl_post_data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $GLOBALS['lipaNaMpesaOnlineURL']);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $accessToken));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    $curl_response = curl_exec($curl);
    return $curl_response;
}

function logError($conn, $transactionId, $errorMessage) {
    $stmt = $conn->prepare("UPDATE transactions SET status = ?, response_description = ? WHERE id = ?");
    $status = 'failed';
    $stmt->bind_param("ssi", $status, $errorMessage, $transactionId);
    $stmt->execute();
    $stmt->close();
}
?>
