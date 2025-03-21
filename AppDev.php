<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}

require_once 'Database.php';

if(isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    
    header("Location: Login.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Development</title>
    <link rel="stylesheet" href="user.css">
</head>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f5f5f5;
        padding: 2rem;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    h1 {
        color: #2c3e50;
        margin-bottom: 1rem;
        font-size: 2.5rem;
        text-align: center;
    }

    p {
        color: #666;
        margin-bottom: 2rem;
        text-align: center;
    }

    #AddUser {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s;
        margin-bottom: 2rem;
    }

    #AddUser:hover {
        background-color: #2980b9;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background-color: white;
        padding: 2rem;
        border-radius: 10px;
        max-width: 500px;
        width: 90%;
        position: relative;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .close-button {
        position: absolute;
        right: 1rem;
        top: 1rem;
        font-size: 1.5rem;
        cursor: pointer;
        background: none;
        border: none;
        color: #666;
        transition: color 0.3s;
    }

    .close-button:hover {
        color: #e74c3c;
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 1.5rem;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    form div {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    label {
        font-weight: 600;
        color: #34495e;
    }

    input, select {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    input:focus, select:focus {
        outline: none;
        border-color: #3498db;
    }

    button[type="submit"] {
        background-color: #2ecc71;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s;
        margin-top: 1rem;
    }

    button[type="submit"]:hover {
        background-color: #27ae60;
    }

    .table-container {
        margin: 20px auto;
        width: 90%;
        max-width: 1200px;
    }

    .button-container {
        display: flex;
        justify-content: flex-start;
        margin-bottom: -4.2rem;
    }

    #AddUser {
        padding: 8px 16px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: background-color 0.3s, transform 0.2s;
    }

    #AddUser:hover {
        background-color: #45a049;
        transform: scale(1.05);
    }

    .table-container th{
        position: sticky;
        top: 0;
        padding:.90rem;
        text-align: center;
        border-bottom: 1px solid #ddd;
        background-color:rgb(208, 231, 255);
        color: #2c3e50;
        font-weight: 600;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2rem;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        min-width: 600px;
        max-width: 1200px;
    }

    
    td{
        padding:.90rem;
        text-align: left;
    }

    tr:hover {
        background-color: #f8f9fa;
    }


    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 1rem;
        margin: 1rem 0;
        border-radius: 4px;
        border-left: 4px solid #dc3545;
    }
    #searchButton{
        background-color:rgb(57, 219, 52);
        color: white;
        border: none;
        padding: 8px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
    }
    #all{
        background-color:rgb(197, 197, 197);
        color: black;
        border: none;
        padding: 8px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;

    }
    #logout{
        background-color: red;
        color: red;
        border: none;
        padding: 12px 24px;
    }

    .logout-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    #logout {
        padding: 8px 16px;
        font-size: 14px;
        background-color: #f44336;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    #logout:hover {
        background-color: #d32f2f;
    }

    /* Reset form margin/padding */
    .logout-form {
        margin: 0;
        padding: 0;
    }

    .welcome-container {
        position: fixed;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        width: 100%;
    }

    .welcome-text {
        font-size: 24px;
        color: #333;
        font-weight: 500;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin-bottom: 20px;
    }

    .description-text {
        font-size: 16px;
        color: #666;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
        padding: 0 20px;
    }

    .title {
        font-size: 32px;
        color: #333;
        font-weight: bold;
        margin-bottom: 15px;
    }

    #successMessage {
        position: fixed;
        top: 20px;
        left: 20px;
        background-color: #d4edda;
        color: #155724;
        padding: 8px 16px;
        border-radius: 4px;
        border-left: 4px solid #28a745;
        font-size: 14px;
        z-index: 1000;
        max-width: 250px;
        animation: fadeOut 5s ease-in-out;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

   

    #failedMessage {
        position: fixed;
        top: 20px;
        left: 20px;
        background-color: #f8d7da;
        color: #721c24;
        padding: 8px 40px;
        border-radius: 4px;
        border-left: 4px solid #dc3545;
        font-size: 14px;
        z-index: 1000;
        max-width: 250px;
        animation: fadeOut 5s ease-in-out;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    @keyframes fadeOut {
        0% { opacity: 1; }
        70% { opacity: 1; }
        100% { opacity: 0; display: none; }
    }

</style>

<body>
    <div>
        <div class="logout-container">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="logout-form">
                <button id="logout" type="submit" name="logout">Logout</button>
            </form>
        </div>
        <h1>User Management</h1>
        <p>  User Management is a system that allows administrators to manage user accounts.</p>
        <div style="display: flex; justify-content: center; gap: 10px;">
        <label style="font-size: 16px; text-align: center; color: #333; font-weight: 500; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin-bottom: 20px;" for="welcome">Welcome, <?php echo $_SESSION['username']; ?>!</label>
        </div>
        <div class="table-container">
            <div class="button-container">
                <button id="AddUser" type="button">Add User</button>
            </div>
            <div style="display: flex; justify-content: center; gap: 10px;">
                <button id="all" type="button">All</button>
                <input style="width: 300px;" type="text" name="search" id="search" placeholder="Search User">
                <button id="searchButton" type="button">Search</button>
            </div>
        </div>
        
        <div class="modal" id="formModal">
            <div class="modal-content">
                <button class="close-button" id="closeModal">&times;</button>
                <h2>Add New User</h2>
                <form action="AppDev.php" method="post">
                    <div>
                        <label for="firstName">First Name:</label>
                        <input type="text" name="firstName" id="firstName" placeholder="Enter First Name" required>
                    </div>
                    <div>
                        <label for="lastName">Last Name:</label>
                        <input type="text" name="lastName" id="lastName" placeholder="Enter Last Name" required>
                    </div>
                    <div>
                        <label for="gender">Gender:</label>
                        <select name="gender" id="gender" required>
                            <option value="">Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="address">Address:</label>
                        <input type="text" name="address" id="address" list="addressList" placeholder="Enter Address" required>
                        <datalist id="addressList">
                            <option value="Alcantara, Cebu">Alcantara, Cebu</option>
                            <option value="Alcoy, Cebu">Alcoy, Cebu</option>
                            <option value="Alegria, Cebu">Alegria, Cebu</option>
                            <option value="Argao, Cebu">Argao, Cebu</option>
                            <option value="Asturias, Cebu">Asturias, Cebu</option>
                            <option value="Badian, Cebu">Badian, Cebu</option>
                            <option value="Balamban, Cebu">Balamban, Cebu</option>
                            <option value="Bantayan, Cebu">Bantayan, Cebu</option>
                            <option value="Barili, Cebu">Barili, Cebu</option>
                            <option value="Bogo, Cebu">Bogo, Cebu</option>
                            <option value="Boljoon, Cebu">Boljoon, Cebu</option>
                            <option value="Borbon, Cebu">Borbon, Cebu</option>
                            <option value="Carcar, Cebu">Carcar, Cebu</option>
                            <option value="Carmen, Cebu">Carmen, Cebu</option>
                            <option value="Catmon, Cebu">Catmon, Cebu</option>
                            <option value="Cebu City, Cebu">Cebu City, Cebu</option>
                            <option value="Compostela, Cebu">Compostela, Cebu</option>
                            <option value="Consolacion, Cebu">Consolacion, Cebu</option>
                            <option value="Cordova, Cebu">Cordova, Cebu</option>
                            <option value="Dalaguete, Cebu">Dalaguete, Cebu</option>
                            <option value="Danao, Cebu">Danao, Cebu</option>
                            <option value="Dumanjug, Cebu">Dumanjug, Cebu</option>
                            <option value="Ginatilan, Cebu">Ginatilan, Cebu</option>
                            <option value="Liloan, Cebu">Liloan, Cebu</option>
                            <option value="Lapu-Lapu, City">Lapu-Lapu, City</option>
                            <option value="Madridejos, Cebu">Madridejos, Cebu</option>
                            <option value="Mandaue, Cebu City">Mandaue, Cebu City</option>
                            <option value="Minglanilla, Cebu">Minglanilla, Cebu</option>
                            <option value="Moalboal, Cebu">Moalboal, Cebu</option>
                            <option value="Oslob, Cebu">Oslob, Cebu</option>
                            <option value="Pilar, Cebu">Pilar, Cebu</option>
                            <option value="Pinamungahan, Cebu">Pinamungahan, Cebu</option>
                            <option value="Poro, Cebu">Poro, Cebu</option>
                            <option value="Ronda, Cebu">Ronda, Cebu</option>
                            <option value="San Fernando, Cebu">San Fernando, Cebu</option>
                            <option value="San Francisco, Cebu">San Francisco, Cebu</option>
                            <option value="San Remigio, Cebu">San Remigio, Cebu</option>
                            <option value="Santa Fe, Cebu">Santa Fe, Cebu</option>
                            <option value="Santander, Cebu">Santander, Cebu</option>
                            <option value="Sibonga, Cebu">Sibonga, Cebu</option>
                            <option value="Sogod, Cebu">Sogod, Cebu</option>
                            <option value="Tabogon, Cebu">Tabogon, Cebu</option>
                            <option value="Tabuelan, Cebu">Tabuelan, Cebu</option>
                            <option value="Talisay, Cebu">Talisay, Cebu</option>
                            <option value="Toledo, Cebu">Toledo, Cebu</option>
                            <option value="Tuburan, Cebu">Tuburan, Cebu</option>
                            <option value="Tudela, Cebu">Tudela, Cebu</option>
                            <option value="Tugbong, Cebu">Tugbong, Cebu</option>
                            <option value="Ulat, Cebu">Ulat, Cebu</option>
                            <option value="Umas, Cebu">Umas, Cebu</option>
                            <option value="Ubay, Cebu">Ubay, Cebu</option>
                            <option value="Valencia, Cebu">Valencia, Cebu</option>
                            <option value="Valladolid, Cebu">Valladolid, Cebu</option>
                            <option value="Zambujal, Cebu">Zambujal, Cebu</option>
                        </datalist>
                    </div>
                    <button type="submit" name="SubmitUser">Register</button>
                </form>
            </div>
        </div>

        <div id="userList"></div>
    </div>

    <script>

        const addUser = document.getElementById('AddUser');
        const modal = document.getElementById('formModal');
        const closeModal = document.getElementById('closeModal');

        addUser.addEventListener('click', () => {
            modal.style.display = 'flex';
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        const searchInput = document.getElementById('search');
        const searchButton = document.getElementById('searchButton');
        const allButton = document.getElementById('all');

        searchButton.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        allButton.addEventListener('click', function() {
            searchInput.value = '';
            window.location.href = 'AppDev.php';
        });

        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase();
            window.location.href = `AppDev.php?search=${encodeURIComponent(searchTerm)}`;
        }
    </script>

    <!-- PHP code to show message -->
    <?php if(isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']); 
            ?>
        </div>
    <?php endif; ?>

    <!-- Add this JavaScript for automatic removal -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('successMessage');
        if(successMessage) {
            setTimeout(() => {
                successMessage.remove();
            }, 5000); // Remove after 5 seconds
        }
    });
    </script>

</body>

</html>

<?php
include 'database.php';

if (isset($_POST['SubmitUser'])) {
    $firstName = mysqli_real_escape_string($connect_database, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($connect_database, $_POST['lastName']);
    $gender = mysqli_real_escape_string($connect_database, $_POST['gender']);
    $address = mysqli_real_escape_string($connect_database, $_POST['address']);

    //Calling all the users
    $checkUser = "SELECT * FROM user WHERE firstName = ? AND lastName = ?";
    $getData = $connect_database->prepare($checkUser);
    $getData->bind_param("ss", $firstName, $lastName);
    $getData->execute();
    $result = $getData->get_result();

    // Check if user already exists
    if ($result->num_rows > 0) {
        echo "<div id='failedMessage' style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 4px;'>User already exists!</div>";
        echo "<script>
                setTimeout(function() {
                    let failedMsg = document.getElementById('failedMessage');
                    if (failedMsg) {
                        failedMsg.style.transition = 'opacity 0.5s';
                        failedMsg.style.opacity = '0';
                        setTimeout(function() {
                            failedMsg.remove();
                        }, 500);
                    }
                }, 5000);
            </script>";
    } else {
        $userId = random_int(111111, 999999);
        $insertIntoDatabase = "INSERT INTO user (userId, firstName, lastName, gender, address) 
                              VALUES (?, ?, ?, ?, ?)";

        $data = $connect_database->prepare($insertIntoDatabase);
        $data->bind_param('issss', $userId, $firstName, $lastName, $gender, $address);

        if ($data->execute()) {
            echo "<div id='successMessage'>New record created successfully</div>";
        } else {
            echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 4px;'>Error creating user: " . $data->error . "</div>";
        }
        $data->close();
    }
    $getData->close();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($connect_database, $_GET['search']) : '';
$DisplayAll = "SELECT * FROM user WHERE 
               firstName LIKE '%$search%' OR 
               lastName LIKE '%$search%' OR 
               userId LIKE '%$search%' OR 
               address LIKE '%$search%'";
$result = $connect_database->query($DisplayAll);

if ($result->num_rows > 0) {
    echo "<div class='table-container'>
            <table border='1'>
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Gender</th>
                <th>Address</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['userId']) . "</td>
                <td>" . htmlspecialchars($row['firstName']) . "</td>
                <td>" . htmlspecialchars($row['lastName']) . "</td>
                <td>" . htmlspecialchars($row['gender']) . "</td>
                <td>" . htmlspecialchars($row['address']) . "</td>
              </tr>";
    }
    echo "</table></div>";
} else {
            echo "<div class='table-container'>
                    <table border='1'>
                        <tr>
                            <th align='center'>No users found</th>
                        </tr>
                    </table>
                </div>";
}

mysqli_close($connect_database);
