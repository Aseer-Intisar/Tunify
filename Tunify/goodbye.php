<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goodbye - Tunify</title>
    <style>
        body.goodbye-body {
            background: url('byebye.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 50px;
            width: 300px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            color: white;
        }

        .container h1 {
            margin-bottom: 20px;
            color: #ff4b4b;
        }

        .container p {
            margin: 10px 0;
            color: #ccc;
        }

        .container a {
            color: #1db954;
            text-decoration: none;
            font-weight: bold;
        }

        .container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="goodbye-body">
    <div class="container">
        <h1>Goodbye!</h1>
        <p>Your account has been deleted successfully.</p>
        <p><a href="index.php">Return to Home</a></p>
    </div>
</body>
</html>