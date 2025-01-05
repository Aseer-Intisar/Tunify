<?php
include("database.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

<?php
include("database.php");

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($query) {
    // Check songs
    $stmt = $conn->prepare("SELECT title FROM songs WHERE title REGEXP ?");
    $pattern = '^' . $query;  // Add ^ to match start of string
    $stmt->bind_param('s', $pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        header("Location: songs.php?searched_song=" . urlencode($row['title']));
        exit();
    }

    // Check albums
    $stmt = $conn->prepare("SELECT name FROM album WHERE name REGEXP ?");
    $pattern = '^' . $query;  // Add ^ to match start of string
    $stmt->bind_param('s', $pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        header("Location: album.php?searched_album=" . urlencode($row['name']));
        exit();
    }

    // No results found
    header("Location: index1.php?error=notfound&query=" . urlencode($query));
    exit();
}

$conn->close();
?>