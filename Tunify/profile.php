
<?php
include("database.php");
session_start();


$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        $name = $_POST['name'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE user SET name = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $password, $user_id);

        if ($stmt->execute()) {
            echo "Profile updated successfully";
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
            font-family: Arial, sans-serif;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 50px;
            width: 300px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .container input {
            width: 96%;
            padding: 11px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }

        .container button {
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

        .container button:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Profile</h1>
        <form method="post" action="profile.php">
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            <input type="password" name="password" placeholder="New Password">
            <button type="submit" name="update">Update Profile</button>
        </form>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Account created on: <?php echo htmlspecialchars($creation_date); ?></p>
        <p>Account type: <?php echo htmlspecialchars($account_type); ?></p>
    </div>
</body>
</html>