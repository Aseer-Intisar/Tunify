<?php
include("database.php");
session_start();

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, email FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

$top_songs = [];
$sql = "SELECT id, title, album_name, genre, duration, listen_count FROM songs ORDER BY listen_count DESC LIMIT 6";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $top_songs[] = $row;
    }
}

$recommended_songs = [];
$playlists = [];
$stmt = $conn->prepare("SELECT id FROM playlist WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $playlists[] = $row['id'];
}
$stmt->close();

if (!empty($playlists)) {
    $placeholders = implode(',', array_fill(0, count($playlists), '?'));
    $types = str_repeat('i', count($playlists));
    $stmt = $conn->prepare("SELECT DISTINCT songs.genre FROM playlist_songs 
                            JOIN songs ON playlist_songs.song_id = songs.id 
                            WHERE playlist_songs.playlist_id IN ($placeholders)");
    $stmt->bind_param($types, ...$playlists);
    $stmt->execute();
    $result = $stmt->get_result();
    $genres = [];
    while ($row = $result->fetch_assoc()) {
        $genres[] = $row['genre'];
    }
    $stmt->close();

    if (!empty($genres)) {
        $placeholders = implode(',', array_fill(0, count($genres), '?'));
        $types = str_repeat('s', count($genres));
        $stmt = $conn->prepare("SELECT songs.id, songs.title, artist.name AS artist_name FROM songs 
                                JOIN artist ON songs.artist_id = artist.id 
                                WHERE songs.genre IN ($placeholders) LIMIT 12");
        $stmt->bind_param($types, ...$genres);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recommended_songs[] = $row;
        }
        $stmt->close();
    }
}

if (empty($recommended_songs)) {
    $stmt = $conn->prepare("SELECT songs.id, songs.title, artist.name AS artist_name FROM songs 
                            JOIN artist ON songs.artist_id = artist.id 
                            ORDER BY songs.listen_count DESC LIMIT 12");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recommended_songs[] = $row;
    }
    $stmt->close();
}

$public_playlists = [];
$sql = "SELECT id, name, like_counter FROM playlist WHERE is_public = 1 ORDER BY like_counter DESC LIMIT 5";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $public_playlists[] = $row;
    }
}

if (isset($_POST['song_id']) && isset($_POST['song_title'])) {
    $song_id = $_POST['song_id'];
    $song_title = $_POST['song_title'];

    $stmt = $conn->prepare("UPDATE songs SET listen_count = listen_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $song_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['now_playing'] = "Now Playing: " . htmlspecialchars($song_title);

    header("Location: index1.php?play=rick.MP3");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunify - Music Streaming Site</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">Tunify</div>

            <form class="search-bar" action="search_results.php" method="GET">
                <input type="text" name="query" placeholder="Search for songs, artists, or albums" required>
                <button type="submit">Search</button>
            </form>
            <?php if(isset($_GET['error']) && $_GET['error'] == 'notfound'): ?>
                <div class="error-message">
                    No results found for "<?php echo htmlspecialchars($_GET['query']); ?>"
                </div>
            <?php endif; ?>

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

            <details class="user-menu">
                <summary>
                    <img src="user-icon.png" alt="User" class="user-icon">
                    <span class="dropdown-icon">&#x25BC;</span>
                </summary>
                <div class="dropdown">
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </details>
        </div>
    </header>

    <section id="hero">
        <div class="hero-content">
            <h1>Discover Your Soundtrack</h1>
            <p>Stream your favorite music and explore new hits.</p>
            <a href="songs.php" class="btn">Start Listening</a>
        </div>
    </section>

    <main>
        <section id="featured">
            <h2>Featured Playlists</h2>
            <div class="playlist-container">
                <?php foreach ($public_playlists as $playlist): ?>
                    <div class="playlist">
                        <h3><a href="featured.php?id=<?php echo htmlspecialchars($playlist['id']); ?>"><?php echo htmlspecialchars($playlist['name']); ?></a></h3>
                        <p>Likes: <span id="like_counter-<?php echo $playlist['id']; ?>"><?php echo htmlspecialchars($playlist['like_counter']); ?></span></p>
                        <?php
                        $stmt = $conn->prepare("SELECT id FROM playlist_likes WHERE user_id = ? AND playlist_id = ?");
                        $stmt->bind_param("ii", $user_id, $playlist['id']);
                        $stmt->execute();
                        $stmt->store_result();
                        $has_liked = $stmt->num_rows > 0;
                        $stmt->close();
                        ?>
                        <form action="like_playlist.php" method="POST" class="like-form">
                            <input type="hidden" name="playlist_id" value="<?php echo $playlist['id']; ?>">
                            <button type="submit" class="<?php echo $has_liked ? 'dislike-button' : 'like-button'; ?>"><?php echo $has_liked ? 'Dislike' : 'Like'; ?></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="recommendations">
            <h2>Recommended For You</h2>
            <div class="recommendations-container">
                <?php foreach ($recommended_songs as $song): ?>
                    <div class="recommendation">
                        <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                        <p>Artist: <?php echo htmlspecialchars($song['artist_name']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="top-charts">
            <h2>Top Charts</h2>
            <div class="top-charts-container">
                <?php foreach ($top_songs as $song): ?>
                    <div class="top-song">
                        <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                        <p>Album: <?php echo htmlspecialchars($song['album_name']); ?></p>
                        <p>Genre: <?php echo htmlspecialchars($song['genre']); ?></p>
                        <p>Duration: <?php echo htmlspecialchars($song['duration']); ?></p>
                        <p>Listen Count: <?php echo htmlspecialchars($song['listen_count']); ?></p>
                        <form action="index1.php" method="POST">
                            <input type="hidden" name="song_id" value="<?php echo $song['id']; ?>">
                            <input type="hidden" name="song_title" value="<?php echo htmlspecialchars($song['title']); ?>">
                            <button type="submit" class="increment-listen-count">Listen</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
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