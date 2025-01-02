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
$conn->close();
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

            <nav>
                <ul class="nav-links">
                    <li><a href="http://localhost/tunify/index1.php">Home</a></li>
                    <li><a href="http://localhost/tunify/songs.php">Songs</a></li>
                    <li><a href="http://localhost/tunify/album.php">Albums</a></li>
                    <li><a href="http://localhost/tunify/artists.php">Artists</a></li>
                    <li><a href="playlists.html">Playlists</a></li>
                    <li><a href="subscriptions.html">Subscriptions</a></li>
                </ul>
            </nav>

            <details class="user-menu">
                <summary>
                    <img src="user-icon.png" alt="User" class="user-icon">
                    <span class="dropdown-icon">&#x25BC;</span>
                </summary>
                <div class="dropdown">
                    <a href="http://localhost/tunify/profile.php">Profile</a>
                    <a href="http://localhost/tunify/logout.php">Logout</a>
                </div>
            </details>
        </div>
    </header>

    <section id="hero">
        <div class="hero-content">
            <h1>Discover Your Soundtrack</h1>
            <p>Stream your favorite music and explore new hits.</p>
            <a href="http://localhost/tunify/songs.php" class="btn">Start Listening</a>
        </div>
    </section>

    <main>
        <section id="featured">
            <h2>Featured Playlists</h2>
            <div class="playlist-container">
                <div class="playlist">
                    <h3>Playlist Title 1</h3>
                </div>
                <div class="playlist">
                    <h3>Playlist Title 2</h3>
                </div>
                <div class="playlist">
                    <h3>Playlist Title 3</h3>
                </div>
                <div class="playlist">
                    <h3>Playlist Title 4</h3>
                </div>
                <div class="playlist">
                    <h3>Playlist Title 5</h3>
                </div>
            </div>
        </section>

        <section id="recommendations">
            <h2>Recommended For You</h2>
            <div class="recommendations-container">
                <div class="recommendation">
                    <h3>Song Title 1</h3>
                    <p>Artist 1</p>
                </div>
                <div class="recommendation">
                    <h3>Song Title 2</h3>
                    <p>Artist 2</p>
                </div>
                <div class="recommendation">
                    <h3>Song Title 3</h3>
                    <p>Artist 3</p>
                </div>
                <div class="recommendation">
                    <h3>Song Title 4</h3>
                    <p>Artist 4</p>
                </div>
                <div class="recommendation">
                    <h3>Song Title 5</h3>
                    <p>Artist 5</p>
                </div>
            </div>
        </section>

        <section id="top-charts">
            <h2>Top Charts</h2>
            <div class="charts-container">
                <div class="chart">
                    <h3>#1: Song Title A</h3>
                    <p>Artist: Artist A</p>
                </div>
                <div class="chart">
                    <h3>#2: Song Title B</h3>
                    <p>Artist: Artist B</p>
                </div>
                <div class="chart">
                    <h3>#3: Song Title C</h3>
                    <p>Artist: Artist C</p>
                </div>
                <div class="chart">
                    <h3>#4: Song Title D</h3>
                    <p>Artist: Artist D</p>
                </div>
                <div class="chart">
                    <h3>#5: Song Title E</h3>
                    <p>Artist: Artist E</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Tunify. All rights reserved.</p>
    </footer>
</body>
</html>
