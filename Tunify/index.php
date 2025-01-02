<?php
include("database.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: index1.php");
            exit();
        } else {
            echo "Invalid email or password";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TUNIFY</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('NEW1.png') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .header {
            font-size: 3rem;
            color: purple;
            font-weight: bold;
            font-family: Impact, sans-serif;
            margin-bottom: 20px;
            text-shadow: none;
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

        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Tunify</div>
        <form method="post" action="index.php">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="button" id="toggle-password">Show Password</button>
            <button type="submit" name="login">Log In</button>
        </form>
        <div class="signup">
            <p>Don't have an account? <a href="signup.html">Sign up</a></p>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('toggle-password');

        togglePasswordButton.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePasswordButton.textContent = 'Hide Password';
            } else {
                passwordInput.type = 'password';
                togglePasswordButton.textContent = 'Show Password';
            }
        });
    </script>
</body>
</html>