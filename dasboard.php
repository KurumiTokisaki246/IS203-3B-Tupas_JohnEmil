<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: LoginForm.php');
    exit();
}

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'jmt';

$connection = mysqli_connect($host, $user, $password, $database);
if (!$connection) {
    die("Error: " . mysqli_connect_error());
}

// Fetch user details for editing if user_id is set
function getUserToEdit($connection) {
    if (isset($_GET['user_id'])) {
        $userId = $_GET['user_id'];
        $stmt = $connection->prepare("SELECT * FROM names WHERE ID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    return null;
}

// Function to create a new user
function createUser($connection) {
    // Implementation here...
}

// Function to update a user
function updateUser($connection) {
    // Implementation here...
}

// Function to delete a user
function deleteUser($connection) {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];
        $stmt = $connection->prepare("DELETE FROM names WHERE ID = ?");
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            echo "<script>alert('User deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error deleting user.');</script>";
        }
    }
}

// Set the user to edit if user_id is provided in GET request
$userToEdit = getUserToEdit($connection);

// Handle user management actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        createUser($connection);
    }

    if (isset($_POST['delete'])) {
        deleteUser($connection);
    }

    if (isset($_POST['update'])) {
        updateUser($connection);
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: LoginForm.php');
        exit();
    }

    // Profile photo upload/update
    if (isset($_POST['upload_photo'])) {
        if (!empty($_FILES['photo']['name'])) {
            $targetDir = "uploads/";
            $fileName = basename($_FILES["photo"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            // Validate file type (e.g., jpg, png, jpeg)
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileType, $allowedTypes)) {
                // Move uploaded file to server
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
                    $stmt = $connection->prepare("UPDATE names SET photo = ? WHERE ID = ?");
                    $stmt->bind_param("si", $targetFilePath, $_SESSION['user_id']);
                    if ($stmt->execute()) {
                        $_SESSION['photo'] = $targetFilePath; // Update session with new photo path
                        echo "<script>alert('Profile photo updated successfully.');</script>";
                    } else {
                        echo "<script>alert('Error updating profile photo.');</script>";
                    }
                } else {
                    echo "<script>alert('Error uploading photo.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.');</script>";
            }
        }
    }
}

// Fetch all registered users
$sqlAccounts = mysqli_query($connection, "SELECT * FROM names");
if (!$sqlAccounts) {
    die("Error: " . mysqli_error($connection));
}

// Fetch user data for sidebar display
$user = getUserToEdit($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        /* Original CSS */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            color: white;
            margin: 0;
            display: flex;
        }
        h1, h2 {
            text-align: center;
        }
        .sidebar {
            background: rgba(0, 0, 0, 0.9);
            padding: 20px;
            border-radius: 10px;
            width: 200px;
            margin: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            position: relative;
            text-align: center;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ffffff;
            color: #000;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 16px;
            cursor: pointer;
        }
        .main {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin: 20px;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            flex: 1;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="file"], input[type="submit"], input[type="button"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 5px 0 20px;
            border: none;
            border-radius: 5px;
        }
        input[type="submit"], input[type="button"] {
            background-color: #4facfe;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover, input[type="button"]:hover {
            background-color: #00f2fe;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid white;
        }
        th {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .actions form {
            display: inline;
        }
        img.user-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <button id="closeSidebarBtn" class="close-btn">&times;</button>
        <h3><?php echo htmlspecialchars(isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''); ?></h3>
        
        <!-- Display user photo -->
        <?php if (!empty($_SESSION['photo'])): ?>
            <img src="<?php echo htmlspecialchars($_SESSION['photo']); ?>" alt="User Photo" class="user-photo">
        <?php else: ?>
            <img src="default-profile.png" alt="Default Profile Photo" class="user-photo">
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="file" name="photo" accept="image/*" required>
            <input type="submit" name="upload_photo" value="Update Photo">
        </form>

        <form method="post">
            <input type="submit" name="logout" value="Log Out">
        </form>
        <form action="change_password.php" method="get">
            <input type="submit" value="Change Password">
        </form>
        <button id="printButton" class="btn btn-primary" onclick="window.print()">Print</button>
    </div>

    <div class="main">
        <h1>Welcome!</h1>
        <h2><?php echo $userToEdit ? "Edit User" : "Create New User"; ?></h2>
        <form method="post" enctype="multipart/form-data">
            <?php if ($userToEdit): ?>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userToEdit['ID']); ?>">
            <?php endif; ?>
            <label>First Name:</label>
            <input type="text" name="first_name" value="<?php echo $userToEdit ? htmlspecialchars($userToEdit['FirstName']) : ''; ?>" required>
            <label>Middle Name:</label>
            <input type="text" name="middle_name" value="<?php echo $userToEdit ? htmlspecialchars($userToEdit['MiddleName']) : ''; ?>">
            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo $userToEdit ? htmlspecialchars($userToEdit['LastName']) : ''; ?>" required>
            <label>Upload Photo:</label>
            <input type="file" name="photo" accept="image/*">
            <input type="submit" name="<?php echo $userToEdit ? 'update' : 'create'; ?>" value="<?php echo $userToEdit ? 'UPDATE' : 'CREATE'; ?>">
        </form>

        <h2>Registered Users</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
            <?php while ($user = mysqli_fetch_assoc($sqlAccounts)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['ID']); ?></td>
                    <td><?php echo htmlspecialchars($user['FirstName']); ?></td>
                    <td><?php echo htmlspecialchars($user['MiddleName']); ?></td>
                    <td><?php echo htmlspecialchars($user['LastName']); ?></td>
                    <td>
                        <?php if (!empty($user['photo'])): ?>
                            <a href="<?php echo htmlspecialchars($user['photo']); ?>" target="_blank">
                                <img src="<?php echo htmlspecialchars($user['photo']); ?>" alt="User Photo" class="user-photo">
                            </a>
                        <?php else: ?>
                            <img src="default-profile.png" alt="Default Photo" class="user-photo">
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <form method="post" action="edit.php?user_id=<?php echo $user['ID']; ?>">
                            <input type="submit" value="Edit">
                        </form>
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?php echo $user['ID']; ?>">
                            <input type="submit" name="delete" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <script>
        document.getElementById('closeSidebarBtn').onclick = function() {
            document.querySelector('.sidebar').style.display = 'none';
        };
    </script>
</body>
</html>
