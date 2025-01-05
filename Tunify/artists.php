<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artists - Tunify</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
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
        <section id="artists">
            <h2>Artists</h2>
            <div class="artist-container">
                <?php
               
                $conn = new mysqli("localhost", "root", "", "tunify");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                
                if (isset($_GET['artist_id'])) {
                    $artist_id = intval($_GET['artist_id']);

                   
                    $artist_query = "SELECT * FROM artist WHERE id = $artist_id";
                    $artist_result = $conn->query($artist_query);

                    if ($artist_result->num_rows > 0) {
                        $artist = $artist_result->fetch_assoc();
                        echo '<h3>' . htmlspecialchars($artist['name']) . '</h3><hr><br>';
                        echo '<p><strong>Bio:</strong> ' . htmlspecialchars($artist['bio']) . '</p><hr><br>';
                        echo '<p><strong>Monthly Listeners:</strong> ' . htmlspecialchars($artist['monthly_listeners']) . '</p><hr><br>';

                      
                        $albums_query = "SELECT * FROM album WHERE artist_id = $artist_id";
                        $albums_result = $conn->query($albums_query);

                        if ($albums_result->num_rows > 0) {
                            echo '<h4>Albums:</h4>';
                            echo '<ul>';
                            while ($album = $albums_result->fetch_assoc()) {
                                echo '<li>' . htmlspecialchars($album['name']) . '</li>';
                            }
                            echo '</ul><hr><br>';
                        } else {
                            echo '<p>No albums found for this artist.</p>';
                        }

                        
                        $songs_query = "
                            SELECT songs.title 
                            FROM songs 
                            JOIN album ON songs.album_name = album.name 
                            WHERE album.artist_id = $artist_id";
                        $songs_result = $conn->query($songs_query);

                        if ($songs_result->num_rows > 0) {
                            echo '<h4>Songs:</h4>';
                            echo '<ul>';
                            while ($song = $songs_result->fetch_assoc()) {
                                echo '<li>' . htmlspecialchars($song['title']) . '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>No songs found for this artist.</p>';
                        }
                    } else {
                        echo '<p>Artist not found.</p>';
                    }
                } else {
                  
                    $artists_query = "SELECT * FROM artist";
                    $artists_result = $conn->query($artists_query);

                    if ($artists_result->num_rows > 0) {
                        while ($artist = $artists_result->fetch_assoc()) {
                            echo '<div class="artist">';
                            echo '<a href="artists.php?artist_id=' . $artist['id'] . '">';
                            echo '<h3>' . htmlspecialchars($artist['name']) . '</h3>';
                            echo '</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No artists found.</p>';
                    }
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
