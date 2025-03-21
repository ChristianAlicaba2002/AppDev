<?php
session_start(); // Start session at the very top
require_once 'Database.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $firstName = mysqli_real_escape_string($connect_database, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($connect_database, $_POST['lastName']);
    $gender = mysqli_real_escape_string($connect_database, $_POST['gender']);
    $username = mysqli_real_escape_string($connect_database, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username or email already exists
    $check_query = "SELECT * FROM user_register WHERE username = ?";
    $check_stmt = $connect_database->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username or email already exists!";
    } else {
        // Insert new user
        $userId = random_int(100000, 999999);
        $insert_query = "INSERT INTO user_register (userId, firstName, lastName, gender, username, password) VALUES (?, ?, ?, ?, ?,?)";
        $insert_stmt = $connect_database->prepare($insert_query);
        $insert_stmt->bind_param("ssssss", $userId, $firstName, $lastName,$gender, $username, $password);
        
        if ($insert_stmt->execute()) {
            $_SESSION['success'] = "Registration successful! Please login.";
            echo "<script>
                alert('Registration successful! Redirecting to login page...');
                setTimeout(function() {
                    window.location.href = 'Login.php';
                }, 2000);
            </script>";
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
    <title>Register</title>
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

        select{
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
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
        select:focus{
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

        .error-message {
            color: red;
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

        <h1>Register</h1>
        <p>Please fill in your details to create an account</p>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <input type="text" name="firstName" placeholder="First Name" required>
            <input type="text" name="lastName" placeholder="Last Name" required>
            <select name="gender" id="">
                <option value="">Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Prefer not to say</option>
            </select>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="button-group">
                <input type="submit" name="register" value="Register">
                <a href="Login.php" class="btn-secondary">Switch to Login</a>
            </div>
        </form>
    </div>
</body>
</html>