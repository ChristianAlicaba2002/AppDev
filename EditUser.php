<?php
session_start();
require_once 'Database.php';

$error = '';
$success = '';

if (!isset($_GET['userId'])) {
    header("Location: AdminDashboard.php");
    exit();
}

$userId = $_GET['userId'];

// Fetch user data
$query = "SELECT * FROM user_register WHERE userId = ?";
$stmt = $connect_database->prepare($query);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: AdminDashboard.php");
    exit();
}

if (isset($_POST['edit_user'])) {
    $firstName = mysqli_real_escape_string($connect_database, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($connect_database, $_POST['lastName']);
    $gender = mysqli_real_escape_string($connect_database, $_POST['gender']);
    $address = mysqli_real_escape_string($connect_database, $_POST['address']);
    $username = mysqli_real_escape_string($connect_database, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $updateQuery = "UPDATE user_register SET firstName = ?, lastName = ?, gender = ?, address = ?, username = ?, password = ? WHERE userId = ?";
    $stmt = $connect_database->prepare($updateQuery);
    $stmt->bind_param("sssssss", $firstName, $lastName, $gender, $address, $username, $password, $userId);

    if ($stmt->execute()) {
        $success = "User updated successfully!";
    } else {
        $error = "Failed to update user. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
            background-color: #007bff;
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
        <h1>Edit User</h1>
        <?php if ($success): ?>
            <div class="message"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <input type="text" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>" required>
            <input type="text" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>" required>
            <select name="gender" required>
                <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
            </select>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <input type="password" name="password" placeholder="New Password" required>
            <button type="submit" name="edit_user">Update User</button>
        </form>
    </div>
</body>
</html> 