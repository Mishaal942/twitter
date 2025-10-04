<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['user_id'])) {
    header("Location: index.php");
    exit();
}

$follower_id = $_SESSION['user_id'];
$following_id = $_POST['user_id'];

try {
    $stmt = $conn->prepare("DELETE FROM followers WHERE follower_id = :follower_id AND following_id = :following_id");
    $stmt->bindParam(':follower_id', $follower_id, PDO::PARAM_INT);
    $stmt->bindParam(':following_id', $following_id, PDO::PARAM_INT);
    $stmt->execute();
    
    header("Location: profile.php?user_id=" . $following_id);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
