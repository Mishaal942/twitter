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

// Fetch the tweets for the logged-in user (or all users, as per your needs)
$query = "SELECT tweets.id AS tweet_id, tweets.tweet_content, tweets.created_at, users.username, users.id AS user_id 
          FROM tweets
          JOIN users ON tweets.user_id = users.id 
          ORDER BY tweets.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Twitter Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #15202B;
            color: #ffffff;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            border-bottom: 1px solid #38444d;
            padding: 15px 0;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
        }

        .tweet-form {
            background-color: #192734;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #38444d;
        }

        .tweet-form h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #ffffff;
        }

        textarea {
            width: 100%;
            background-color: #15202B;
            border: 1px solid #38444d;
            border-radius: 10px;
            padding: 12px;
            color: #ffffff;
            font-size: 16px;
            resize: none;
            margin-bottom: 10px;
        }

        textarea:focus {
            border-color: #1DA1F2;
            outline: none;
        }

        .tweet-btn {
            background-color: #1DA1F2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 9999px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .tweet-btn:hover {
            background-color: #1a91da;
        }

        .tweets-section {
            margin-top: 20px;
        }

        .tweet {
            padding: 15px;
            border-bottom: 1px solid #38444d;
            background-color: #192734;
            border-radius: 15px;
            margin-bottom: 15px;
        }

        .tweet-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .username {
            font-weight: bold;
            color: #ffffff;
            text-decoration: none;
        }

        .username:hover {
            text-decoration: underline;
        }

        .tweet-content {
            margin: 10px 0;
            font-size: 15px;
            line-height: 1.5;
        }

        .tweet-time {
            color: #8899a6;
            font-size: 14px;
        }

        .nav-links {
            margin: 20px 0;
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: #1DA1F2;
            text-decoration: none;
            font-size: 15px;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Twitter Clone</h1>
        </div>

        <div class="tweet-form">
            <h2>Post a New Tweet</h2>
            <form method="POST" action="post_tweet.php">
                <textarea name="tweet" rows="4" maxlength="160" placeholder="What's happening?" required></textarea>
                <button type="submit" class="tweet-btn">Tweet</button>
            </form>
        </div>

        <div class="tweets-section">
            <h2>Latest Tweets</h2>
            <?php if ($tweets): ?>
                <?php foreach ($tweets as $tweet): ?>
                    <div class="tweet">
                        <div class="tweet-header">
                            <a href="profile.php?user_id=<?php echo $tweet['user_id']; ?>" class="username">
                                <?php echo htmlspecialchars($tweet['username']); ?>
                            </a>
                        </div>
                        <div class="tweet-content">
                            <?php echo htmlspecialchars($tweet['tweet_content']); ?>
                        </div>
                        <div class="tweet-time">
                            <?php echo date('M j', strtotime($tweet['created_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tweets to display.</p>
            <?php endif; ?>
        </div>

        <div class="nav-links">
            <a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>">My Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
