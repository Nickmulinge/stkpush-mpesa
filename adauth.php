<?php
session_start(); // Start session if not already started

// Database connection parameters
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "stkpush";

// Create connection
$con = new mysqli($servername, $dbusername, $dbpassword, $database);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if form is submitted for login
if (isset($_POST['loginbtn'])) {
    // Get user input
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if form data is being received
    if (empty($username) || empty($password)) {
        $_SESSION['message'] = "Please fill all fields.";
        header('Location: index.php');
        exit();
    }

    // Query to check if username exists and password matches
    $stmt = $con->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user was found
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Set session variables and redirect to dashboard
        $_SESSION['admin'] = $row['username'];
        $_SESSION['message'] = "You logged in successfully";
        header("Location: admindashboard.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid username or password.";
        header('Location: index.php');
        exit();
    }

    $stmt->close();
}

// Close connection
$con->close();
?>
