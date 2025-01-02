<?php
include("database.php");

$query = isset($_GET['q']) ? $_GET['q'] : '';

$songs = [];
if ($query) {
    $stmt = $conn->prepare("
        SELECT 
            songs.title AS song_title, 
            artist.name AS artist_name
        FROM songs
        JOIN artist ON songs.artist_id = artist.id
        WHERE songs.title LIKE ? OR artist.name LIKE ?
        LIMIT 10
    ");
    $searchTerm = '%' . $query . '%';
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $stmt->bind_result($song_title, $artist_name);

    while ($stmt->fetch()) {
        $songs[] = [
            'title' => $song_title,
            'artist_name' => $artist_name
        ];
    }

    $stmt->close();
}

$conn->close();

echo json_encode($songs);
?>