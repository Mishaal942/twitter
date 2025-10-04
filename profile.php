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

// Get the profile user_id from URL, if not set use logged in user's id
$profile_id = $_GET['user_id'] ?? $_SESSION['user_id'];

try {
    // Get user information
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :profile_id");
    $stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get user's tweets
    $tweet_stmt = $conn->prepare("SELECT * FROM tweets WHERE user_id = :profile_id ORDER BY created_at DESC");
    $tweet_stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
    $tweet_stmt->execute();
    $tweets = $tweet_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if logged in user is following this profile
    $follow_stmt = $conn->prepare("SELECT * FROM followers WHERE follower_id = :follower_id AND following_id = :following_id");
    $follow_stmt->bindParam(':follower_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $follow_stmt->bindParam(':following_id', $profile_id, PDO::PARAM_INT);
    $follow_stmt->execute();
    $is_following = $follow_stmt->rowCount() > 0;

    // Get follower and following counts
    $follower_count_stmt = $conn->prepare("SELECT COUNT(*) FROM followers WHERE following_id = :profile_id");
    $follower_count_stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
    $follower_count_stmt->execute();
    $follower_count = $follower_count_stmt->fetchColumn();

    $following_count_stmt = $conn->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = :profile_id");
    $following_count_stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
    $following_count_stmt->execute();
    $following_count = $following_count_stmt->fetchColumn();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($user['username']); ?></title>
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
            position: sticky;
            top: 0;
            background-color: rgba(21, 32, 43, 0.95);
            backdrop-filter: blur(12px);
            z-index: 1000;
        }

        h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-header {
            padding: 20px 0;
            border-bottom: 1px solid #38444d;
        }

        .profile-stats {
            display: flex;
            gap: 20px;
            margin: 15px 0;
            color: #8899a6;
        }

        .stat {
            display: flex;
            gap: 5px;
        }

        .stat-number {
            color: #ffffff;
            font-weight: bold;
        }

        .follow-btn {
            background-color: #1DA1F2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 9999px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .follow-btn:hover {
            background-color: #1a91da;
        }

        .unfollow-btn {
            background-color: transparent;
            color: #fff;
            border: 1px solid #1DA1F2;
            padding: 10px 20px;
            border-radius: 9999px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .unfollow-btn:hover {
            background-color: rgba(29, 161, 242, 0.1);
        }

        .tweets-section {
            margin-top: 20px;
        }

        .tweet {
            padding: 15px 0;
            border-bottom: 1px solid #38444d;
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

        .back-link {
            display: inline-block;
            color: #1DA1F2;
            text-decoration: none;
            margin-top: 20px;
            font-size: 15px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* Navigation */
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
            <h1>Profile</h1>
            <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
        </div>

        <div class="profile-header">
            <?php if ($profile_id != $_SESSION['user_id']): ?>
                <?php if ($is_following): ?>
                    <form action="unfollow.php" method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $profile_id; ?>">
                        <button type="submit" class="unfollow-btn">Following</button>
                    </form>
                <?php else: ?>
                    <form action="follow.php" method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $profile_id; ?>">
                        <button type="submit" class="follow-btn">Follow</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Profile Stats -->
            <div class="profile-stats">
                <div class="stat">
                    <span class="stat-number"><?php echo $following_count; ?></span>
                    <span>Following</span>
                </div>
                <div class="stat">
                    <span class="stat-number"><?php echo $follower_count; ?></span>
                    <span>Followers</span>
                </div>
            </div>
        </div>

        <!-- User's Tweets -->
        <div class="tweets-section">
            <?php if ($tweets): ?>
                <?php foreach ($tweets as $tweet): ?>
                    <div class="tweet">
                        <div class="tweet-content">
                            <?php echo htmlspecialchars($tweet['tweet_content']); ?>
                        </div>
                        <div class="tweet-time">
                            <?php echo date('M j', strtotime($tweet['created_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tweets yet.</p>
            <?php endif; ?>
        </div>

        <div class="nav-links">
            <a href="index.php">Back to Home</a>
        </div>
    </div>
</body>
</html>
