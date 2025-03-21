<?php
session_start();
require_once 'Database.php';  

// If user is already logged in, redirect to AppDev.php
if(isset($_SESSION['username'])) {
    header("Location: AdminDashboard.php");
    exit();
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($connect_database, $_POST['email']);
    $password = $_POST['password'];
    
    // Query to check if user exists with username (temporarily using username instead of email)
    $query = "SELECT * FROM user_register WHERE username = ?";
    $stmt = $connect_database->prepare($query);
    $stmt->bind_param("s", $email); // Using email input field to store username temporarily
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, create session
            $_SESSION['user_id'] = $user['userId'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to AppDev.php
            header("Location: AdminLTE/dist/pages/index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Username not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            color: #333;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        p {
            color: #666;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        input[type="text"],
        input[type="password"] {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4CAF50;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .button-group input[type="submit"] {
            flex: 1;
        }

        .btn-secondary {
            flex: 1;
            background-color: #ffffff;
            color: #4CAF50;
            padding: 12px;
            border: 2px solid #4CAF50;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
        }

        .login-link {
            color: #4CAF50;
            text-decoration: none;
            text-align: center;
            display: block;
            margin-bottom: 1rem;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .email-container,
        .password-container {
            position: relative;
            width: 100%;
            margin-bottom: 1rem;
        }

        .email-container input,
        .password-container input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .email-container input:focus,
        .password-container input:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .password-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .email-container input {
            width: 100%;
        }
        .forgot-password a{
            text-decoration: none;
            color: #4CAF50;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <p>Log into your account to continue</p>
        <form action="Login.php" method="post">
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="email-container">
                <input type="text" name="email" id="email" placeholder="Username" required>
            </div>

            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>
            <div class="button-group">
                <input type="submit" name="login" value="Login">
                <a href="Register.php" class="btn-secondary">Switch to Register</a>
            </div>
            <div class="forgot-password" style="text-align: center; margin-top: 1rem;">
                <a href="ForgotPassword.php">Forgot Password?</a>
            </div>
        </form>
    </div>

    <script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this;
        
        // Toggle the password visibility
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Disable back button
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
    };
    </script>
</body>
</html>