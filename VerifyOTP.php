<?php
session_start();
require_once 'Database.php';

// Redirect if no temp user data
if (!isset($_SESSION['temp_user'])) {
    header("Location: Register.php");
    exit();
}

$error = '';
$success = '';

// Resend OTP
if (isset($_POST['resend_otp'])) {
    // Generate new OTP
    $otp = rand(100000, 999999);
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    $_SESSION['temp_user']['otp'] = $otp;
    $_SESSION['temp_user']['otp_expiry'] = $otp_expiry;
    
    // Send OTP email
    $email = $_SESSION['temp_user']['email'];
    sendOTPEmail($email, $otp);
    
    $success = "New OTP has been sent to your email.";
}

// Function to send OTP email (same as in Register.php)
function sendOTPEmail($email, $otp) {
    $subject = "Email Verification OTP";
    $message = "Your OTP for email verification is: $otp\n\nThis code will expire in 10 minutes.";
    $headers = "From: christiandave120702@gmail.com";
    
    mail($email, $subject, $message, $headers);
}

// Verify OTP
if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    $stored_otp = $_SESSION['temp_user']['otp'];
    $otp_expiry = $_SESSION['temp_user']['otp_expiry'];
    
    // Check if OTP is expired
    if (date('Y-m-d H:i:s') > $otp_expiry) {
        $error = "OTP has expired. Please request a new one.";
    } 
    // Check if OTP matches
    elseif ($entered_otp != $stored_otp) {
        $error = "Invalid OTP. Please try again.";
    } 
    // OTP is valid, register the user
    else {
        $userId = random_int(100000, 999999);
        $firstName = $_SESSION['temp_user']['firstName'];
        $lastName = $_SESSION['temp_user']['lastName'];
        $gender = $_SESSION['temp_user']['gender'];
        $email = $_SESSION['temp_user']['email'];
        $password = $_SESSION['temp_user']['password'];
        
        // Insert new user
        $insert_query = "INSERT INTO user_register (userId, firstName, lastName, gender, email, password, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $insert_stmt = $connect_database->prepare($insert_query);
        $insert_stmt->bind_param("ssssss", $userId, $firstName, $lastName, $gender, $email, $password);
        
        if ($insert_stmt->execute()) {
            // Clear temp user data
            unset($_SESSION['temp_user']);
            
            // Set success message
            $_SESSION['success'] = "Registration successful! Your email has been verified. Please login.";
            
            // Redirect to login
            header("Location: Login.php");
            exit();
        } else {
            $error = "Registration failed! Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
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

        input[type="text"] {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
            text-align: center;
            letter-spacing: 0.5em;
            font-weight: bold;
        }

        input[type="text"]:focus {
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
        }

        .email-info {
            margin-top: 10px;
            padding: 10px;
            background-color: #e9f7ef;
            border-radius: 4px;
            text-align: center;
        }

        .timer {
            font-weight: bold;
            color: #e74c3c;
            margin-top: 10px;
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
    </style>
</head>
<body>
    <div class="container">
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <h1>Verify Your Email</h1>
        <p>We've sent a 6-digit code to your email</p>
        
        <div class="email-info">
            OTP sent to: <strong><?php echo htmlspecialchars(substr_replace($_SESSION['temp_user']['email'], '***', 3, strpos($_SESSION['temp_user']['email'], '@') - 3)); ?></strong>
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <input type="text" name="otp" placeholder="Enter OTP" maxlength="6" required>
            <div class="timer" id="timer">OTP expires in: 1:00</div>
            <input type="submit" name="verify_otp" value="Verify OTP">
            <div class="button-group">
                <input type="submit" name="resend_otp" value="Resend OTP">
                <a href="Register.php" class="btn-secondary">Back to Register</a>
            </div>
        </form>
    </div>

    <script>
    // OTP Timer
    let timeLeft = 60; // 10 minutes in seconds
    const timerElement = document.getElementById('timer');
    
    const countdown = setInterval(function() {
        const minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        seconds = seconds < 1 ? '0' + seconds : seconds;
        
        timerElement.innerHTML = `OTP expires in: ${minutes}:${seconds}`;
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
            timerElement.innerHTML = 'OTP has expired';
        }
        timeLeft--;
    }, 1000);
    </script>
</body>
</html> 