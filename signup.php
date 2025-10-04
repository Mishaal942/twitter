<?php
// Ensure no output is sent to the browser before session_start
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include database connection
require 'db.php';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate form inputs
    if (!empty($username) && !empty($email) && !empty($password)) {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Prepare and execute the insert statement
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);

            // Redirect to the login page
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error during signup: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>All fields are required. Please fill out the form.</p>";
    }
}
?>

<!-- Signup Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Twitter Clone</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .signup-container {
            background-color: #192734;
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo svg {
            width: 40px;
            height: 40px;
            fill: #1DA1F2;
        }

        h1 {
            font-size: 23px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #8899a6;
            font-size: 15px;
        }

        input {
            width: 100%;
            padding: 12px;
            background-color: #15202B;
            border: 1px solid #38444d;
            border-radius: 5px;
            color: #ffffff;
            font-size: 16px;
            transition: border-color 0.2s;
        }

        input:focus {
            border-color: #1DA1F2;
            outline: none;
            box-shadow: 0 0 0 2px rgba(29, 161, 242, 0.2);
        }

        .signup-btn {
            width: 100%;
            background-color: #1DA1F2;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 9999px;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
        }

        .signup-btn:hover {
            background-color: #1a91da;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #8899a6;
            font-size: 14px;
        }

        .login-link a {
            color: #1DA1F2;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background-color: #8b141a;
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .requirements {
            font-size: 13px;
            color: #8899a6;
            margin-top: 5px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header p {
            color: #8899a6;
            font-size: 15px;
            margin-top: 10px;
        }

        /* Animation for focus states */
        input:focus {
            animation: glow 0.5s ease-in-out;
        }

        @keyframes glow {
            0% { box-shadow: 0 0 0 0 rgba(29, 161, 242, 0.2); }
            70% { box-shadow: 0 0 0 4px rgba(29, 161, 242, 0.2); }
            100% { box-shadow: 0 0 0 2px rgba(29, 161, 242, 0.2); }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="logo">
            <!-- Twitter Logo SVG -->
            <svg viewBox="0 0 24 24">
                <path d="M23.643 4.937c-.835.37-1.732.62-2.675.733.962-.576 1.7-1.49 2.048-2.578-.9.534-1.897.922-2.958 1.13-.85-.904-2.06-1.47-3.4-1.47-2.572 0-4.658 2.086-4.658 4.66 0 .364.042.718.12 1.06-3.873-.195-7.304-2.05-9.602-4.868-.4.69-.63 1.49-.63 2.342 0 1.616.823 3.043 2.072 3.878-.764-.025-1.482-.234-2.11-.583v.06c0 2.257 1.605 4.14 3.737 4.568-.392.106-.803.162-1.227.162-.3 0-.593-.028-.877-.082.593 1.85 2.313 3.198 4.352 3.234-1.595 1.25-3.604 1.995-5.786 1.995-.376 0-.747-.022-1.112-.065 2.062 1.323 4.51 2.093 7.14 2.093 8.57 0 13.255-7.098 13.255-13.254 0-.2-.005-.402-.014-.602.91-.658 1.7-1.477 2.323-2.41z"/>
            </svg>
        </div>

        <div class="form-header">
            <h1>Create your account</h1>
            <p>Join Twitter today</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="signup.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <div class="requirements">Choose a unique username</div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <div class="requirements">Use 8 or more characters</div>
            </div>

            <button type="submit" class="signup-btn">Sign up</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</body>
</html>
