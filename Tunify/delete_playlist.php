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

if ($playlist_id > 0) {
  
    $stmt = $conn->prepare("DELETE FROM playlist_songs WHERE playlist_id = ?");
    $stmt->bind_param("i", $playlist_id);
    $stmt->execute();
    $stmt->close();


    $stmt = $conn->prepare("DELETE FROM playlist WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $playlist_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    header("Location: playlists.php?message=Playlist deleted successfully");
} else {
    header("Location: playlists.php?message=Error deleting playlist");
}

$conn->close();
exit;
?>