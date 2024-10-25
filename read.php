<?php

require('./database.php');



// Ensure the connection is successful

if (!$connection) {

    die("Connection failed: " . mysqli_connect_error());

}



// Correctly spelling 'query'

$queryAccounts = "SELECT * FROM names";



// Execute the query and check for success

$sqlAccounts = mysqli_query($connection, $queryAccounts);



if (!$sqlAccounts) {

    die("Error executing query: " . mysqli_error($connection));

}



// Optionally, you could fetch results here if needed

// while ($results = mysqli_fetch_array($sqlAccounts)) {

//     // Process each row

// }

?>