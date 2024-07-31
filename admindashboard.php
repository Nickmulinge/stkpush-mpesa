<?php
session_start();

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

// Fetch admin details
$admin_username = $_SESSION['admin'];
$stmt = $con->prepare("SELECT * FROM admin WHERE username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $admin = $result->fetch_assoc();
} else {
    echo "Error: Admin details not found.";
    exit();
}

$stmt->close();

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $new_username = $_POST['new_username'] ?? '';
//     $new_password = $_POST['new_password'] ?? '';

//     if (!empty($new_username)) {
//         $stmt = $con->prepare("UPDATE admin SET username = ? WHERE id = ?");
//         $stmt->bind_param("si", $new_username, $admin['id']);
//         $stmt->execute();
//         $stmt->close();
//         $_SESSION['admin'] = $new_username;
//         $admin['username'] = $new_username;
//     }

//     if (!empty($new_password)) {
//         $stmt = $con->prepare("UPDATE admin SET password = ? WHERE id = ?");
//         $stmt->bind_param("si", $new_password, $admin['id']);
//         $stmt->execute();
//         $stmt->close();
//     }

//     header('Location: admindashboard.php');
//     exit();
// }

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h1>
        </header>
        <section class="admin-info">
            <h2>Your Information</h2>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
        </section>
        <!-- <section class="admin-update">
            <h2>Update Your Details</h2>
            <form action="" method="post">
                <label for="new_username">New Username:</label><br>
                <input type="text" id="new_username" name="new_username"><br><br>
                <label for="new_password">New Password:</label><br>
                <input type="password" id="new_password" name="new_password"><br><br>
                <button type="submit">Update</button>
            </form>
        </section> -->
        <nav>
            <ul>
                <li><a href="transactions.php">Transactions Management</a></li>
                <li><a href="admins.php">Admins Management</a></li>
                <li><a href="callback_transactions.php">Callback Transactions</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
