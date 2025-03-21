<?php
session_start();
require_once 'Database.php';

// Redirect if not verified
if(!isset($_SESSION['reset_user'])) {
    header("Location: ForgotPassword.php");
    exit();
}

$error = '';
$success = '';

if(isset($_POST['reset'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $userId = $_SESSION['reset_user']['userId'];
        
        $query = "UPDATE user_register SET password = ? WHERE userId = ?";
        $stmt = $connect_database->prepare($query);
        $stmt->bind_param("ss", $hashed_password, $userId);
        
        if($stmt->execute()) {
            $success = "Password reset successful!";
            // Clear reset session
            unset($_SESSION['reset_user']);
            // Redirect after 2 seconds
            header("refresh:2;url=Login.php");
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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

        input {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }

        input:focus {
            outline: none;
            border-color: #4CAF50;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
        }

        button:hover {
            background-color: #45a049;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }

        .weak {
            color: #dc3545;
        }

        .medium {
            color: #ffc107;
        }

        .strong {
            color: #28a745;
        }

        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .success-message {
            color: #28a745;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input {
            width: 100%;
        }

        .password-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <h1>Reset Password</h1>
        <p>Enter your new password</p>

        <form action="" method="post">
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="New Password" required>
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>
            <div id="password-strength" class="password-strength"></div>
            
            <div class="password-container">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <i class="fas fa-eye" id="toggleConfirmPassword"></i>
            </div>
            <button type="submit" name="reset">Reset Password</button>
        </form>
    </div>

    <script>
    // Password visibility toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        togglePasswordVisibility('password', this);
    });
    
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        togglePasswordVisibility('confirm_password', this);
    });
    
    function togglePasswordVisibility(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Password strength checker
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthDiv = document.getElementById('password-strength');
        
        // Check password strength
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        if(password.length < 8) {
            strengthDiv.innerHTML = 'Weak password';
            strengthDiv.className = 'password-strength weak';
        } else if(password.length >= 8 && 
                 ((hasUpperCase && hasLowerCase) || 
                  (hasNumbers && hasSpecialChar))) {
            strengthDiv.innerHTML = 'Medium password';
            strengthDiv.className = 'password-strength medium';
        } else if(password.length >= 8 && 
                 hasUpperCase && hasLowerCase && 
                 hasNumbers && hasSpecialChar) {
            strengthDiv.innerHTML = 'Strong password';
            strengthDiv.className = 'password-strength strong';
        }
    });
    </script>
</body>
</html> 