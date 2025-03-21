<?php
session_start();
require_once 'Database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Redirect if no reset user data
if (!isset($_SESSION['reset_user'])) {
    header("Location: ForgotPassword.php");
    exit();
}

$error = '';
$success = '';

function sendOTPEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'christiandave120702@gmail.com'; // SMTP username
        $mail->Password = 'december72002'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('christiandave120702@gmail.com', 'Christian Dave');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset OTP';
        $mail->Body    = "Your OTP for password reset is: $otp<br>This code will expire in 5 minutes.";

        $mail->send();
        echo 'OTP has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['resend_otp'])) {
    $otp = rand(100000, 999999);
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
    
    $_SESSION['reset_user']['otp'] = $otp;
    $_SESSION['reset_user']['otp_expiry'] = $otp_expiry;

    $email = $_SESSION['reset_user']['username']; 
    sendOTPEmail($email, $otp);
    
    $success = "New OTP has been sent.";
}

if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    $stored_otp = $_SESSION['reset_user']['otp'];
    $otp_expiry = $_SESSION['reset_user']['otp_expiry'];
    
    if (date('Y-m-d H:i:s') > $otp_expiry) {
        $error = "OTP has expired. Please request a new one.";
    } 
    elseif ($entered_otp != $stored_otp) {
        $error = "Invalid OTP. Please try again.";
    } 
    else {
        header("Location: ResetPassword.php");
        exit();
    }
}

// mail('christiandave120702@gmail.com', 'Test Email', 'This is a test email.');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
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

        <h1>Verify Your Identity</h1>
        <p>We've sent a 6-digit code to your email</p>
        
        <div class="email-info">
            OTP sent to: <strong><?php 
                $email = $_SESSION['reset_user']['username']; // Using username as email temporarily
                echo htmlspecialchars(substr_replace($email, '***', 3, min(5, strlen($email) - 3))); 
            ?></strong>
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <input type="text" name="otp" placeholder="Enter OTP" maxlength="6" required>
            <div class="timer" id="timer">OTP expires in: 05:00</div>
            <input type="submit" name="verify_otp" value="Verify OTP">
            <div class="button-group">
                <input type="submit" name="resend_otp" value="Resend OTP">
                <a href="ForgotPassword.php" class="btn-secondary">Back</a>
            </div>
        </form>
    </div>

    <script>
    // OTP Timer
    let timeLeft = 300; // 5 minutes in seconds
    const timerElement = document.getElementById('timer');
    
    const countdown = setInterval(function() {
        const minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        
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