<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums - Tunify</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo">Tunify</div>
        <nav>
            <ul>
                <li><a href="index1.php">Home</a></li>
                <li><a href="songs.php">Songs</a></li>
                <li><a href="albums.php">Albums</a></li>
                <li><a href="artists.html">Artists</a></li>
                <li><a href="playlists.php">Playlists</a></li>
                <li><a href="subscriptions.php">Subscriptions</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="albums">
            <h2>Albums</h2>
            <div class="album-container">
                <?php
                $conn = new mysqli("localhost", "root", "", "tunify");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                if (isset($_GET['album_id'])) {
                    $album_id = intval($_GET['album_id']);
                    $sql = "
                        SELECT album.name AS album_name, artist.name AS artist_name, songs.title AS song_title 
                        FROM album 
                        JOIN artist ON album.artist_id = artist.id 
                        JOIN songs ON songs.album_name = album.name 
                        WHERE album.id = $album_id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo '<h3>Album Details</h3><hr>';
                        $row = $result->fetch_assoc();
                        echo '<h4>Album: ' . htmlspecialchars($row['album_name']) . '</h4><hr>';
                        
                        echo '<h4>Artist: ' . htmlspecialchars($row['artist_name']) . '</h4><hr><br>';
                        echo '<ul>';
                        do {
                            echo '<li>' . htmlspecialchars($row['song_title']) . '</li>';
                        } while ($row = $result->fetch_assoc());
                        echo '</ul>';
                    } else {
                        echo '<p>No songs found for this album.</p>';
                    }
                } else {
                    // Fetch all albums
                    $sql = "SELECT album.id, album.name AS album_name, artist.name AS artist_name 
                            FROM album 
                            JOIN artist ON album.artist_id = artist.id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="album">';
                            echo '<a href="album.php?album_id=' . $row['id'] . '">';
                            echo '<img src="album2.png" alt="' . htmlspecialchars($row['album_name']) . '">';
                            echo '<h3>' . htmlspecialchars($row['album_name']) . '</h3>';
                            echo '<p>Artist: ' . htmlspecialchars($row['artist_name']) . '</p>';
                            echo '</a>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p>No albums found.</p>";
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
