<?php
// the next lines define constants for the database connection parameters, including the host, username, password, and database name. 
// These constants are used to establish a connection to the MySQL database using the mysqli extension. The connection
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'greenfield');

// here you create a new mysqli object using the defined constants to connect to the database. If the connection fails, it will output an error message and terminate the script.
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// check to see if the connection was successful. 
// If there is a connection error, the script will die and output the error message. 
// If the connection is successful, it will set the character set to utf8 to ensure proper encoding of data.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// set the character set for the connection to utf8, which is important for handling international characters and ensuring that data is stored and retrieved correctly from the database.
$conn->set_charset("utf8");
?>