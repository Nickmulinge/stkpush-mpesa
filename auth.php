<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "stkpush";

// Create connection
$con = new mysqli($servername, $username, $password, $database);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Sample admin users with hashed passwords
$admins = [
    ['username' => 'admin1', 'email' => 'admin1@example.com', 'password' => password_hash('password1', PASSWORD_DEFAULT)],
    ['username' => 'admin2', 'email' => 'admin2@example.com', 'password' => password_hash('password2', PASSWORD_DEFAULT)]
];

foreach ($admins as $admin) {
    $stmt = $con->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin['username'], $admin['email'], $admin['password']);
    $stmt->execute();
}

$stmt->close();
$con->close();
?>
