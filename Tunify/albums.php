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
                <li><a href="http://localhost/tunify/index1.php">Home</a></li>
                <li><a href="http://localhost/tunify/songs.php">Songs</a></li> 
                <li><a href="albums.html">Albums</a></li>
                <li><a href="artists.html">Artists</a></li>
                <li><a href="playlists.html">Playlists</a></li>
                <li><a href="subscriptions.html">Subscriptions</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="albums">
            <h2>Albums</h2>
            <div class="album-container">
                <?php
                // Database connection
                $conn = new mysqli('localhost', 'correct_username', 'correct_password', 'database');

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch albums from the database
                $sql = "SELECT id, title, artist, img_url FROM albums";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="album">';
                        echo '<a href="albums.php?album_id=' . $row["id"] . '">';
                        echo '<img src="' . $row["img_url"] . '" alt="' . $row["title"] . '">';
                        echo '<h3>' . $row["title"] . '</h3>';
                        echo '<p>' . $row["artist"] . '</p>';
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo "0 results";
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
