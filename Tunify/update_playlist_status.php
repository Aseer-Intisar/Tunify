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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['playlist_id']) && isset($_POST['is_public'])) {
    $playlist_id = intval($_POST['playlist_id']);
    $is_public = intval($_POST['is_public']);


    $stmt = $conn->prepare("UPDATE playlist SET is_public = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $is_public, $playlist_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $message = "Playlist status updated successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();

   
    header("Location: view_playlist.php?id=$playlist_id&message=" . urlencode($message));
    exit;
}

$conn->close();
?>