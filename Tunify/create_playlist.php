<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tunify";

$conn = new mysqli($servername, $username, $password, $dbname);
session_start();
$user_id = $_SESSION['user_id'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$playlist_name = $_POST['playlist_name'];
$created_at = date("Y-m-d");
$is_public = 1; 


$sql = "INSERT INTO playlist (name, created_at, is_public, user_id)
        VALUES ('$playlist_name', '$created_at', $is_public, $user_id)";




if ($conn->query($sql) === TRUE) {
    echo "New playlist created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta http-equiv="refresh" content="2;url=playlists.php">
</head>
<body>
    <p> Rederecting to playlist page....</p>
</body>
</html>
