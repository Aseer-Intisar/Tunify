<?php
session_start();
include("database.php");

if (!isset($_SESSION['user_id'])) {
    echo "Error: User not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];
$playlist_id = isset($_POST['playlist_id']) ? intval($_POST['playlist_id']) : 0;


$stmt = $conn->prepare("SELECT id FROM playlist_likes WHERE user_id = ? AND playlist_id = ?");
$stmt->bind_param("ii", $user_id, $playlist_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
   
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM playlist_likes WHERE user_id = ? AND playlist_id = ?");
    $stmt->bind_param("ii", $user_id, $playlist_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE playlist SET like_counter = like_counter - 1 WHERE id = ?");
    $stmt->bind_param("i", $playlist_id);
    $stmt->execute();
    $stmt->close();
} else {
  
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO playlist_likes (user_id, playlist_id, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $user_id, $playlist_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE playlist SET like_counter = like_counter + 1 WHERE id = ?");
    $stmt->bind_param("i", $playlist_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: index1.php");
exit;
?>