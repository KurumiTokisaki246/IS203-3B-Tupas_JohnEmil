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



$connection = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());



$message = '';





if (isset($_POST['change_password'])) {

    $currentPassword = mysqli_real_escape_string($connection, $_POST['current_password']);

    $newPassword = mysqli_real_escape_string($connection, $_POST['new_password']);

    $confirmPassword = mysqli_real_escape_string($connection, $_POST['confirm_password']);



    // Fetch user details

    $userId = $_SESSION['user_id'];

    $result = mysqli_query($connection, "SELECT * FROM users WHERE ID='$userId'");

    $user = mysqli_fetch_assoc($result);



    if ($user && password_verify($currentPassword, $user['PasswordHash'])) {

        if ($newPassword === $confirmPassword) {

            // Hash the new password

            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);



            // Update the password in the database

            $updateQuery = "UPDATE users SET PasswordHash = ? WHERE ID = ?";

            $stmt = mysqli_prepare($connection, $updateQuery);

            mysqli_stmt_bind_param($stmt, "si", $hashedNewPassword, $userId);

            if (mysqli_stmt_execute($stmt)) {

                $message = "Password changed successfully!";

            } else {

                $message = "Error updating password: " . mysqli_error($connection);

            }

            mysqli_stmt_close($stmt);

        } else {

            $message = "New passwords do not match!";

        }

    } else {

        $message = "Current password is incorrect!";

    }

}

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password</title>

    <style>

        body {

            font-family: 'Georgia', serif;

            background-color: #f8f1e5;

            color: #5b3a29;

            margin: 0;

            padding: 20px;

            display: flex;

            justify-content: center;

            align-items: center;

            height: 100vh;

        }

        h1 {

            text-align: center;

            font-size: 2.5em;

            margin-bottom: 20px;

        }

        .main {

            background-color: #fff4e1;

            padding: 30px;

            border: 2px solid #d6b56d;

            border-radius: 10px;

            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);

            width: 300px;

        }

        label {

            font-size: 1.1em;

            margin-top: 10px;

            display: block;

        }

        input[type="password"] {

            width: 100%;

            padding: 8px;

            margin-top: 5px;

            margin-bottom: 20px;

            border: 1px solid #d6b56d;

            border-radius: 5px;

            font-size: 1em;

        }

        input[type="submit"] {

            background-color: #d6b56d;

            color: #fff;

            border: none;

            padding: 10px;

            border-radius: 5px;

            cursor: pointer;

            font-size: 1.2em;

            transition: background-color 0.3s;

        }

        input[type="submit"]:hover {

            background-color: #b49b5e;

        }

        .message {

            color: red;

            text-align: center;

            margin-bottom: 20px;

        }

    </style>

</head>

<body>



    <h1>Change Password</h1>

    

    <div class="main">

        <?php if ($message) { ?>

            <div class="message"><?php echo htmlspecialchars($message); ?></div>

        <?php } ?>

        

        <form method="post">

            <label>Current Password:</label>

            <input type="password" name="current_password" required>

            <label>New Password:</label>

            <input type="password" name="new_password" required>

            <label>Confirm New Password:</label>

            <input type="password" name="confirm_password" required>

            <input type="submit" name="change_password" value="Change Password">

        </form>


        <a href="dasboard.php" class="button" style="margin-top: 10px;">Return</a>
    </div>



</body>

</html>