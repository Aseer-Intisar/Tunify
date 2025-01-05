<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playlists - Tunify</title>
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
                <li><a href="subscriptions.html">Subscriptions</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="playlists">
            <h2>Playlists</h2>
            
            <div class="create-playlist">
                <h3>Create a New Playlist</h3>
                <form action="playlists.php" method="POST">
                    <label for="playlist_name">Playlist Name:</label>
                    <input type="text" id="playlist_name" name="playlist_name" placeholder="Enter playlist name" required>
                    <button type="submit">Create Playlist</button>
                </form>
            </div>

            <div class="playlist-container">
                <?php

                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "tunify";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                session_start(); 

                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['playlist_name'])) {
                    $playlist_name = $_POST['playlist_name'];
                    $created_at = date("Y-m-d");
                    $is_public = 0; 

                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];

                        $stmt = $conn->prepare("INSERT INTO playlist (name, created_at, user_id, is_public) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("ssii", $playlist_name, $created_at, $user_id, $is_public);

                        if ($stmt->execute()) {
                            echo "New playlist created successfully";
                        } else {
                            echo "Error: " . $stmt->error;
                        }

                        $stmt->close();
                    } else {
                        echo "Error: User not logged in.";
                    }
                }

                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $stmt = $conn->prepare("SELECT id, name, is_public FROM playlist WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="playlist">';
                            echo '<h3><a href="view_playlist.php?id=' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</a></h3>';
                            echo '<p>Public: <span class="public-status ' . ($row['is_public'] ? 'yes' : 'no') . '">' . ($row['is_public'] ? 'Yes' : 'No') . '</span></p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No playlists available. Create your first playlist above!</p>';
                    }

                    $stmt->close();
                } else {
                    echo '<p>Error: User not logged in.</p>';
                }

                $conn->close();
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tunify. All rights reserved.</p>
    </footer>
</body>
</html>