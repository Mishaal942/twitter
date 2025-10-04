<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['user_id'])) {
    header("Location: index.php");
    exit();
}

$follower_id = $_SESSION['user_id'];
$following_id = $_POST['user_id'];

// Don't allow users to follow themselves
if ($follower_id == $following_id) {
    header("Location: profile.php?user_id=" . $following_id);
    exit();
}

try {
    // Check if already following
    $check_stmt = $conn->prepare("SELECT * FROM followers WHERE follower_id = :follower_id AND following_id = :following_id");
    $check_stmt->bindParam(':follower_id', $follower_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':following_id', $following_id, PDO::PARAM_INT);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() == 0) {
        // If not already following, insert new follow relationship
        $stmt = $conn->prepare("INSERT INTO followers (follower_id, following_id) VALUES (:follower_id, :following_id)");
        $stmt->bindParam(':follower_id', $follower_id, PDO::PARAM_INT);
        $stmt->bindParam(':following_id', $following_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    header("Location: profile.php?user_id=" . $following_id);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
