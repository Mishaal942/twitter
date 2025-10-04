<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['tweet'])) {
    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO tweets (user_id, tweet_content) VALUES (:user_id, :tweet_content)");
        
        // Bind parameters
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':tweet_content', $_POST['tweet'], PDO::PARAM_STR);
        
        // Execute the statement
        $stmt->execute();
        
        // Redirect back to index page
        header("Location: index.php");
        exit();
        
    } catch (PDOException $e) {
        die("Error posting tweet: " . $e->getMessage());
    }
} else {
    // If no tweet content, redirect back to index
    header("Location: index.php");
    exit();
}
?>
