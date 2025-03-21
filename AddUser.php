<?php
session_start();
require_once 'Database.php';

$error = '';
$success = '';

if (isset($_POST['add_user'])) {
    $firstName = mysqli_real_escape_string($connect_database, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($connect_database, $_POST['lastName']);
    $gender = mysqli_real_escape_string($connect_database, $_POST['gender']);
    $address = mysqli_real_escape_string($connect_database, $_POST['address']);
    $username = mysqli_real_escape_string($connect_database, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $userId = random_int(100000, 999999);

    $insertQuery = "INSERT INTO user (userId,	firstName,	lastName,	gender	,address	) VALUES (?, ?, ?, ?, ?)";
    $stmt = $connect_database->prepare($insertQuery);
    $stmt->bind_param("sssss", $userId, $firstName, $lastName, $gender, $address);

    if ($stmt->execute()) {
        $success = "User added successfully!";
    } else {
        $error = "Failed to add user. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input, select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            opacity: 0.9;
        }

        .message {
            text-align: center;
            margin-bottom: 10px;
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add User</h1>
        <?php if ($success): ?>
            <div class="message"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <input type="text" name="firstName" placeholder="First Name" required>
            <input type="text" name="lastName" placeholder="Last Name" required>
            <select name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="add_user">Add User</button>
        </form>
    </div>
</body>
</html> 