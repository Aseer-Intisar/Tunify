<?php
include("database.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: songs.php");
            exit();
        } else {
            $error_message = "Invalid email or password";
        }

        $stmt->close();
    }
    $conn->close();
}

if (!isset($_SESSION['user_id'])) {
    // Show login form if user is not logged in
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>TUNIFY</title>
        <style>
            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background: url('NEW1.png') no-repeat center center fixed;
                background-size: cover;
                background-position: center;
                color: white;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .header {
                font-size: 3rem;
                color: purple;
                font-weight: bold;
                font-family: Impact, sans-serif;
                margin-bottom: 20px;
                text-shadow: none;
            }

            .container {
                background: rgba(0, 0, 0, 0.7);
                border-radius: 10px;
                padding: 50px;
                width: 300px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
                text-align: center;
            }

            .container input {
                width: 96%;
                padding: 11px;
                margin: 10px 0;
                border: none;
                border-radius: 5px;
            }

            .container button {
                width: 70%;
                padding: 10px;
                margin: 10px 0;
                background: linear-gradient(45deg, #1db954, #1aa34a);
                color: white;
                border: none;
                border-radius: 40px;
                cursor: pointer;
                box-shadow: 0 0 10px #1db954;
            }

            .container button:hover {
                transform: scale(1.05);
            }

            .error-message {
                color: red;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">Tunify</div>
            <form method="post" action="songs.php">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="button" id="toggle-password">Show Password</button>
                <button type="submit" name="login">Log In</button>
            </form>
            <div class="signup">
                <p>Don't have an account? <a href="signup.html">Sign up</a></p>
            </div>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </div>

        <script>
            const passwordInput = document.getElementById('password');
            const togglePasswordButton = document.getElementById('toggle-password');

            togglePasswordButton.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePasswordButton.textContent = 'Hide Password';
                } else {
                    passwordInput.type = 'password';
                    togglePasswordButton.textContent = 'Show Password';
                }
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}

$user_id = $_SESSION['user_id'];

$songs = [];
$sql = "SELECT id, title, album_name, genre, duration, listen_count FROM songs";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
}

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
        <section id="songs">
            <h2>Songs</h2>
            <ul class="songs-container">
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