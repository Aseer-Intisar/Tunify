
<?php
include("database.php");
session_start();

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plan = $_POST['plan'];

    $stmt = $conn->prepare("UPDATE subscription_table SET status = ? WHERE user_id = ?");
    $stmt->bind_param("si", $plan, $user_id);

    if ($stmt->execute()) {
        header("Location: subscriptions.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>