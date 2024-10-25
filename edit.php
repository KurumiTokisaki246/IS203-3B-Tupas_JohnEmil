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

    die("Connection failed: " . mysqli_connect_error());

}



// Logout functionality

if (isset($_POST['logout'])) {

    session_destroy();

    header('Location: LoginForm.php');

    exit();

}



// Function to get user details for editing

function getUserToEdit($connection) {

    $userId = (int)$_GET['user_id'];

    $query = "SELECT * FROM names WHERE ID = ?";

    $stmt = mysqli_prepare($connection, $query);

    mysqli_stmt_bind_param($stmt, "i", $userId);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $userToEdit = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $userToEdit;

}



// Function to update a user

function updateUser($connection) {

    $userId = (int)$_POST['user_id'];

    $firstName = mysqli_real_escape_string($connection, $_POST['first_name']);

    $middleName = mysqli_real_escape_string($connection, $_POST['middle_name']);

    $lastName = mysqli_real_escape_string($connection, $_POST['last_name']);



    $updateQuery = "UPDATE names SET FirstName = ?, MiddleName = ?, LastName = ? WHERE ID = ?";

    $stmt = mysqli_prepare($connection, $updateQuery);

    mysqli_stmt_bind_param($stmt, "sssi", $firstName, $middleName, $lastName, $userId);



    if (mysqli_stmt_execute($stmt)) {

        header('Location: user_management.php'); // Redirect to main user management page after update

        exit();

    } else {

        echo "Error updating user: " . mysqli_error($connection);

    }

    mysqli_stmt_close($stmt);

}



// Handle update request

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

    updateUser($connection);

}



// Fetch user details for editing

$userToEdit = getUserToEdit($connection);

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=