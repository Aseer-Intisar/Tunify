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
            header("Location: index.php");
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
            <form method="post" action="index.php">
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
                <input type="text" name="query" placeholder="Search for songs" required>
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