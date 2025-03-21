<?php
session_start();
require_once 'Database.php';

$error = '';
$success = '';

// Function to generate OTP
function generateOTP() {
    return rand(100000, 999999);
}

// Function to send OTP email
function sendOTPEmail($email, $otp) {
    $subject = "Password Reset OTP";
    $message = "Your OTP for password reset is: $otp\n\nThis code will expire in 10 minutes.";
    $headers = "From: yoursystem@example.com";
    
    mail($email, $subject, $message, $headers);
}

if(isset($_POST['verify'])) {
    $email = mysqli_real_escape_string($connect_database, $_POST['email']);
    
    // Check if user exists with provided email
    $query = "SELECT * FROM user_register WHERE username = ?";  // Temporarily using username field
    $stmt = $connect_database->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Generate OTP
        $otp = generateOTP();
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Store user data and OTP in session
        $_SESSION['reset_user'] = $user;
        $_SESSION['reset_user']['otp'] = $otp;
        $_SESSION['reset_user']['otp_expiry'] = $otp_expiry;
        
        // Send OTP email
        sendOTPEmail($email, $otp);
        
        // Redirect to verify OTP page
        header("Location: VerifyResetOTP.php");
        exit();
    } else {
        $error = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
            width: 100%;
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
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .back-to-login {
            text-align: center;
            margin-top: 1rem;
        }

        .back-to-login a {
            color: #4CAF50;
            text-decoration: none;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <p>Please enter your email to reset your password</p>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <input type="text" name="email" placeholder="Username or Email" required>
            <button type="submit" name="verify">Send Verification Code</button>
        </form>
        
        <div class="back-to-login">
            <a href="Login.php">Back to Login</a>
        </div>
    </div>
</body>
</html> 