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


$playlist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;


$stmt = $conn->prepare("SELECT name, created_at, is_public FROM playlist WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $playlist_id, $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($playlist_name, $created_at, $is_public);
$stmt->fetch();
$stmt->close();


$songs_stmt = $conn->prepare("SELECT songs.id, songs.title, artist.name AS artist_name FROM playlist_songs 
                              JOIN songs ON playlist_songs.song_id = songs.id 
                              JOIN artist ON playlist_songs.artist_id = artist.id 
                              WHERE playlist_songs.playlist_id = ?");
$songs_stmt->bind_param("i", $playlist_id);
$songs_stmt->execute();
$songs_result = $songs_stmt->get_result();

$all_songs_stmt = $conn->prepare("SELECT id, title FROM songs");
$all_songs_stmt->execute();
$all_songs_result = $all_songs_stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Playlist - Tunify</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">Tunify</div>
        <nav>
            <ul>
                <li><a href="http://localhost/tunify/index1.php">Home</a></li>
                <li><a href="http://localhost/tunify/songs.php">Songs</a></li>
                <li><a href="http://localhost/tunify/album.php">Albums</a></li>
                <li><a href="http://localhost/tunify/artists.php">Artists</a></li>
                <li><a href="http://localhost/tunify/playlists.php">Playlists</a></li>
                <li><a href="subscriptions.php">Subscriptions</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="playlist-details">
            <h2><?php echo htmlspecialchars($playlist_name); ?></h2>
            <p>Created on: <?php echo htmlspecialchars($created_at); ?></p>
            <p>Public: <span class="public-status <?php echo $is_public ? 'yes' : 'no'; ?>"><?php echo $is_public ? 'Yes' : 'No'; ?></span></p>

            <form action="update_playlist_status.php" method="POST">
                <input type="hidden" name="playlist_id" value="<?php echo $playlist_id; ?>">
                <label for="is_public">Change Public Status:</label>
                <select id="is_public" name="is_public" class="public-dropdown" required>
                    <option value="1" <?php echo $is_public ? 'selected' : ''; ?>>Yes</option>
                    <option value="0" <?php echo !$is_public ? 'selected' : ''; ?>>No</option>
                </select>
                <button type="submit">Update Status</button>
            </form>

            <?php if (isset($_GET['message'])): ?>
                <p class="message"><?php echo htmlspecialchars($_GET['message']); ?></p>
            <?php endif; ?>

            <h3>Songs in this Playlist:</h3>
            <ul>
                <?php
                if ($songs_result->num_rows > 0) {
                    while ($song = $songs_result->fetch_assoc()) {
                        echo '<li>' . htmlspecialchars($song['title']) . ' (Artist: ' . htmlspecialchars($song['artist_name']) . ') <form action="delete_song_from_playlist.php" method="POST" style="display:inline;">
                                <input type="hidden" name="playlist_id" value="' . $playlist_id . '">
                                <input type="hidden" name="song_id" value="' . htmlspecialchars($song['id']) . '">
                                <button type="submit">Remove</button>
                              </form></li>';
                    }
                } else {
                    echo '<p>No songs in this playlist.</p>';
                }
                $songs_stmt->close();
                ?>
            </ul>

            <h3>Add Songs to Playlist:</h3>
            <form action="add_song_to_playlist.php" method="POST">
                <input type="hidden" name="playlist_id" value="<?php echo $playlist_id; ?>">
                <label for="song_id">Select Song:</label>
                <select name="song_id" id="song_id" required>
                    <?php
                    while ($song = $all_songs_result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($song['id']) . '">' . htmlspecialchars($song['title']) . '</option>';
                    }
                    $all_songs_stmt->close();
                    ?>
                </select>
                <button type="submit">Add Song</button>
            </form>

            <h3>Delete Playlist:</h3>
            <form action="delete_playlist.php" method="POST">
                <input type="hidden" name="playlist_id" value="<?php echo $playlist_id; ?>">
                <button type="submit" class="delete-playlist">Delete Playlist</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tunify. All rights reserved.</p>
    </footer>
</body>
</html>