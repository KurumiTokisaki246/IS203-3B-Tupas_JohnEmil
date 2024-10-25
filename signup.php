<?php

session_start();



// Database connection

$host = 'localhost';

$user = 'root';

$password = '';

$database = 'jmt';



$connection = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());



$message = ''; // For feedback messages



// User registration (Sign-Up)

if (isset($_POST['signup'])) {

    $firstName = mysqli_real_escape_string($connection, $_POST['first_name']);

    $middleName = mysqli_real_escape_string($connection, $_POST['middle_name']);

    $lastName = mysqli_real_escape_string($connection, $_POST['last_name']);

    $email = mysqli_real_escape_string($connection, $_POST['email']);

    $password = mysqli_real_escape_string($connection, $_POST['password']);

    

    // Hash the password

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 



    // Check if email already exists

    $emailCheck = mysqli_query($connection, "SELECT * FROM users WHERE Email='$email'");

    if (mysqli_num_rows($emailCheck) > 0) {

        $message = "Email already exists!";

    } else {

        $query = "INSERT INTO users (FirstName, MiddleName, LastName, Email, PasswordHash) VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($connection, $query);

        mysqli_stmt_bind_param($stmt, "sssss", $firstName, $middleName, $lastName, $email, $hashedPassword);

        if (mysqli_stmt_execute($stmt)) {

            $message = "User registered successfully!";

            header('Location: LoginForm.php'); 

            exit();

        } else {

            $message = "Error: " . mysqli_error($connection);

        }

    }

}

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up</title>

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

        input[type="text"],

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



    <h1>Sign Up</h1>

    

    <div class="main">

        <?php if ($message) { ?>

            <div class="message"><?php echo htmlspecialchars($message); ?></div>

        <?php } ?>

        

        <form method="post">

            <label>First Name:</label>

            <input type="text" name="first_name" required>

            <label>Middle Name:</label>

            <input type="text" name="middle_name">

            <label>Last Name:</label>

            <input type="text" name="last_name" required>

            <label>Email:</label>

            <input type="email" name="email" required>

            <label>Password:</label>

            <input type="password" name="password" required>

            <input type="submit" name="signup" value="SIGN UP">

        </form>

        

        <p>Already have an account? <a href="LoginForm.php">Login here</a></p>

    </div>



</body>

</html