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


$stmt = $conn->prepare("SELECT artist_id FROM songs WHERE id = ?");
$stmt->bind_param("i", $song_id);
$stmt->execute();
$stmt->bind_result($artist_id);
$stmt->fetch();
$stmt->close();

if ($artist_id) {
   
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM playlist_songs WHERE playlist_id = ? AND song_id = ?");
    $check_stmt->bind_param("ii", $playlist_id, $song_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
       
        $message = "Song already exists in the playlist.";
    } else {
       
        $stmt = $conn->prepare("INSERT INTO playlist_songs (playlist_id, song_id, artist_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $playlist_id, $song_id, $artist_id);

        if ($stmt->execute()) {
            $message = "Song added to playlist successfully";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    $message = "Error: Artist not found for the song.";
}

$conn->close();

header("Location: view_playlist.php?id=" . $playlist_id . "&message=" . urlencode($message));
exit;
?>