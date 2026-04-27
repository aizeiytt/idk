<?php
/* DATABASE CONNECTION CONFIG 
   We wrap this in a 'try-catch' block to handle errors gracefully 
   instead of letting the website crash with ugly text.
*/

$host = 'localhost';
$db   = 'greenfield_db'; // Change this to your database name
$user = 'root';         // Default for Laragon
$pass = '';             // Default for Laragon
$charset = 'utf8mb4';   // Support for special characters and emojis

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throws errors if SQL is wrong
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Returns data as an associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Uses real prepared statements for security
];

try {
     // Create the connection object ($pdo)
     $pdo = new PDO($dsn, $user, $pass, $options);
     // Connection successful!
} catch (\PDOException $e) {
     // If connection fails, stop the script and show the error
     die("Connection failed: " . $e->getMessage());
}
?>