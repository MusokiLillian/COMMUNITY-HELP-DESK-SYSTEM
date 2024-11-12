<?php
$host = 'localhost'; // or your database server
$dbname = 'community_helpdesk'; // Database name
$username = 'root'; // your MySQL username
$password = ''; // your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
