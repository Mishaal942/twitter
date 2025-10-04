<?php
$host = 'localhost';
$dbname = 'dbnd5assbp6ibj';
$username = 'uuqiigt1ijlka';
$password = 'wtkrugrjbo4v';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
