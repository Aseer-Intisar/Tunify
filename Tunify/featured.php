<?php
include("database.php");
session_start();

$playlist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;


$stmt = $conn->prepare("SELECT name FROM playlist WHERE id = ?");
$stmt->bind_param("i", $playlist_id);
$stmt->execute();
$stmt->bind_result($playlist_name);
$stmt->fetch();
$stmt->close();


$songs = [];
$stmt = $conn->prepare("SELECT songs.title, artist.name AS artist_name FROM playlist_songs 
                        JOIN songs ON playlist_songs.song_id = songs.id 
                        JOIN artist ON songs.artist_id = artist.id 
                        WHERE playlist_songs.playlist_id = ?");
$stmt->bind_param("i", $playlist_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $songs[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($playlist_name); ?> - Tunify</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">Tunify</div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index1.php">Home</a></li>
                    <li><a href="songs.php">Songs</a></li>
                    <li><a href="album.php">Albums</a></li>
                    <li><a href="artists.php">Artists</a></li>
                    <li><a href="playlists.php">Playlists</a></li>
                    <li><a href="subscriptions.html">Subscriptions</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section id="playlist-songs">
            <h2><?php echo htmlspecialchars($playlist_name); ?></h2>
            <ul class="songs-container">
                <?php foreach ($songs as $song): ?>
                    <li class="song">
                        <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                        <p>Artist: <?php echo htmlspecialchars($song['artist_name']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tunify. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>