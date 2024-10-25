<?php
session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'jmt';

$connection = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());

$message = ''; // For feedback messages

// User login
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    $result = mysqli_query($connection, "SELECT * FROM users WHERE Email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['PasswordHash'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['user_name'] = $user['FirstName'];
        header('Location: dasboard.php'); // Redirect to dashboard upon successful login
        exit();
    } else {
        $message = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            margin: 0 auto; /* Center the h1 element */
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
        input[type="email"],
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
        p {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="main">
        <h1>Login</h1> <!-- Centered Login text -->
        <?php if ($message) { ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php } ?>

        <form method="post">
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <input type="submit" name="login" value="LOGIN">
        </form>

        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>

</body>
</html>
