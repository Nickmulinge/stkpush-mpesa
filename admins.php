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
    $sql = "DELETE FROM admin WHERE id = $id";
    if ($con->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $con->error;
    }
}

// Check if action is update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Ensure to hash the password before storing it

    $sql = "UPDATE admin SET name='$name', username='$username', email='$email', password='$password' WHERE id = $id";
    if ($con->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $con->error;
    }
}

// Fetch admin details
$sql = "SELECT * FROM admin";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
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
    <h2>Admin Management</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Password</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <button onclick="showModal('<?php echo $row['id']; ?>', '<?php echo $row['name']; ?>', '<?php echo $row['username']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['password']; ?>')">Update</button>
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
            <form action="admins.php" method="post">
                <input type="hidden" name="id" id="adminId">
                <label for="name">Name:</label>
                <input type="text" name="name" id="adminName" required><br><br>
                <label for="username">Username:</label>
                <input type="text" name="username" id="adminUsername" required><br><br>
                <label for="email">Email:</label>
                <input type="email" name="email" id="adminEmail" required><br><br>
                <label for="password">Password:</label>
                <input type="password" name="password" id="adminPassword" required><br><br>
                <button type="submit" name="update" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Update</button>
            </form>
        </div>
    </div>

    <footer>
        <p>Check out my GitHub: <a href="https://github.com/nickmulinge" target="_blank">github</a></p>
    </footer>

    <script>
        function showModal(id, name, username, email, password) {
            document.getElementById('adminId').value = id;
            document.getElementById('adminName').value = name;
            document.getElementById('adminUsername').value = username;
            document.getElementById('adminEmail').value = email;
            document.getElementById('adminPassword').value = password;
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
