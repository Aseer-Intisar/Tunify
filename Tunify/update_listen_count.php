<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tunify";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$song_id = isset($_POST['song_id']) ? intval($_POST['song_id']) : 0;
$song_title = isset($_POST['song_title']) ? $_POST['song_title'] : '';

if ($song_id > 0) {
    $stmt = $conn->prepare("UPDATE songs SET listen_count = listen_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $song_id);
    $stmt->execute();
    $stmt->close();

    
    $_SESSION['now_playing'] = "Now Playing: " . htmlspecialchars($song_title);
}

$conn->close();

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>