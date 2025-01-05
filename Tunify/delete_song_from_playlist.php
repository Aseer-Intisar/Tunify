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

if (!isset($_SESSION['user_id'])) {
    echo "Error: User not logged in.";
    exit;
}

$playlist_id = isset($_POST['playlist_id']) ? intval($_POST['playlist_id']) : 0;
$song_id = isset($_POST['song_id']) ? intval($_POST['song_id']) : 0;

$stmt = $conn->prepare("DELETE FROM playlist_songs WHERE playlist_id = ? AND song_id = ?");
$stmt->bind_param("ii", $playlist_id, $song_id);

if ($stmt->execute()) {
    echo "Song deleted from playlist successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: view_playlist.php?id=" . $playlist_id);
exit;
?>