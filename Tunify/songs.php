<?php
include("database.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$songs = [];
$search_query = isset($_GET['query']) ? $_GET['query'] : '';
$sql = "SELECT id, title, album_name, genre, duration, listen_count FROM songs";
if ($search_query) {
    $sql .= " WHERE title LIKE ? OR album_name LIKE ? OR genre LIKE ?";
}
$stmt = $conn->prepare($sql);
if ($search_query) {
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
}
$stmt->close();

$now_playing = isset($_SESSION['now_playing']) ? $_SESSION['now_playing'] : "No song is currently playing.";

if (isset($_POST['song_id']) && isset($_POST['song_title'])) {
    $song_id = $_POST['song_id'];
    $song_title = $_POST['song_title'];

    $stmt = $conn->prepare("UPDATE songs SET listen_count = listen_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $song_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['now_playing'] = "Now Playing: " . htmlspecialchars($song_title);

    header("Location: songs.php?play=rick.MP3");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Songs - Tunify</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">Tunify</div>
            <form class="search-bar" action="songs.php" method="GET">
                <input type="text" name="query" placeholder="Search for songs, artists, or albums" value="<?php echo htmlspecialchars($search_query); ?>" required>
                <button type="submit">Search</button>
            </form>
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
        <section id="songs">
            <h2>Songs</h2>
            <ul class="songs-container">
                <?php if (empty($songs)): ?>
                    <li class="song">No results found for "<?php echo htmlspecialchars($search_query); ?>"</li>
                <?php else: ?>
                    <?php foreach ($songs as $song): ?>
                        <li class="song">
                            <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                            <p>Album: <?php echo htmlspecialchars($song['album_name']); ?></p>
                            <p>Genre: <?php echo htmlspecialchars($song['genre']); ?></p>
                            <p>Duration: <?php echo htmlspecialchars($song['duration']); ?></p>
                            <p>Listen Count: <?php echo htmlspecialchars($song['listen_count']); ?></p>
                            <form action="songs.php" method="POST">
                                <input type="hidden" name="song_id" value="<?php echo $song['id']; ?>">
                                <input type="hidden" name="song_title" value="<?php echo htmlspecialchars($song['title']); ?>">
                                <button type="submit" class="increment-listen-count">Listen</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </section>

        <section id="now-playing">
            <h2>Now Playing</h2>
            <p id="now-playing-song"><?php echo $now_playing; ?></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tunify. All rights reserved.</p>
    </footer>

    <audio id="audio-player" controls <?php if (isset($_GET['play'])) echo 'autoplay'; ?> style="display:block;">
        <source src="rick.MP3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</body>
</html>

<?php
$conn->close();
?>