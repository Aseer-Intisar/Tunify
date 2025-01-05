<?php
include("database.php");
session_start();

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_name'])) {
        $name = $_POST['name'];

        $stmt = $conn->prepare("UPDATE user SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $user_id);

        if ($stmt->execute()) {
            echo "Name updated successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['update_password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password, $user_id);

        if ($stmt->execute()) {
            echo "Password updated successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        // Delete related records in subscription_table
        $stmt = $conn->prepare("DELETE FROM subscription_table WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Delete user record
        $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            session_destroy();
            header("Location: goodbye.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$stmt = $conn->prepare("
    SELECT u.name, u.email, u.creation_date, s.status 
    FROM user u 
    LEFT JOIN subscription_table s ON u.id = s.user_id 
    WHERE u.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $creation_date, $account_type);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Tunify</title>
    <style>
        body {
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .profile-container {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 50px;
            width: 300px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            color: white;
        }

        .profile-container h1 {
            margin-bottom: 10px;
            color: #1db954;
        }

        .profile-container h2 {
            margin-bottom: 20px;
            color: #ffffff;
        }

        .profile-container input {
            width: 96%;
            padding: 11px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: #333;
            color: white;
        }

        .profile-container button {
            width: 70%;
            padding: 10px;
            margin: 10px 0;
            background: linear-gradient(45deg, #1db954, #1aa34a);
            color: white;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            box-shadow: 0 0 10px #1db954;
        }

        .profile-container button:hover {
            transform: scale(1.05);
        }

        .delete-button {
            background: linear-gradient(45deg, #ff4b4b, #ff0000);
            box-shadow: 0 0 10px #ff4b4b;
        }

        .delete-button:hover {
            transform: scale(1.05);
        }

        .profile-container p {
            margin: 10px 0;
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Profile</h1>
        <h2><?php echo htmlspecialchars($name); ?></h2>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Account created on: <?php echo htmlspecialchars($creation_date); ?></p>
        <p>Account type: <?php echo htmlspecialchars($account_type); ?></p>
        
        <form method="post" action="profile.php">
            <h3>Change Name</h3>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            <button type="submit" name="update_name">Update Name</button>
        </form>
        
        <form method="post" action="profile.php">
            <h3>Change Password</h3>
            <input type="password" name="password" placeholder="New Password" required>
            <button type="submit" name="update_password">Update Password</button>
        </form>
        
        <form method="post" action="profile.php">
            <button type="submit" name="delete" class="delete-button">Delete Account</button>
        </form>
    </div>
</body>
</html>