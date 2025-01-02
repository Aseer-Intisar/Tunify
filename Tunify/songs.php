<!-- filepath: /c:/xampp/htdocs/Tunify/songs.php -->
<?php
include("database.php");

// Fetching songs from the database
$songs = [];
$stmt = $conn->prepare("
    SELECT 
        songs.title AS song_title, 
        album.name AS album_title, 
        artist.name AS artist_name, 
        songs.duration AS song_duration, 
        songs.genre AS song_genre
    FROM songs
    JOIN album ON songs.album_name = album.name
    JOIN artist ON songs.artist_id = artist.id
");
$stmt->execute();
$stmt->bind_result($song_title, $album_title, $artist_name, $song_duration, $song_genre);

while ($stmt->fetch()) {
    $songs[] = [
        'song_title' => $song_title,
        'album_title' => $album_title,
        'artist_name' => $artist_name,
        'song_duration' => $song_duration,
        'song_genre' => $song_genre
    ];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Songs - Tunify</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo">Tunify</div>
        <nav>
            <ul>
                <li><a href="http://localhost/tunify/index1.php">Home</a></li>
                <li><a href="http://localhost/tunify/songs.php" class="active">Songs</a></li>
                <li><a href="http://localhost/tunify/album.php">Albums</a></li>
                <li><a href="http://localhost/tunify/artists.php">Artists</a></li>
                <li><a href="playlists.html">Playlists</a></li>
                <li><a href="subscriptions.html">Subscriptions</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="songs">
            <h1>All Songs</h1>
            <div class="songs-container">
                <?php foreach ($songs as $song): ?>
                    <div class="song">
                        <h3><?php echo htmlspecialchars($song['song_title']); ?></h3>
                        <p><strong>Album:</strong> <?php echo htmlspecialchars($song['album_title']); ?></p>
                        <p><strong>Artist:</strong> <?php echo htmlspecialchars($song['artist_name']); ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($song['song_duration']); ?></p>
                        <p><strong>Genre:</strong> <?php echo htmlspecialchars($song['song_genre']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tunify. All rights reserved.</p>
    </footer>
</body>
</html>