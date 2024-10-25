<?php



$host = 'localhost';

$user = 'root';

$password = '';

$database = 'jmt';



$connection = mysqli_connect($host, $user, $password, $database);



if (mysqli_connect_error()) {

    echo "Error: Unable to connect to MySQL <br>";

    echo "Message: " . mysqli_connect_error() . "<br>";

    exit; // Stop execution if connection fails

}



// If connection is successful, you can add a success message if needed

// echo "Successfully connected to the database";



?>
