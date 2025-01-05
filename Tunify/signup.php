
<?php
include("database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $created = date("Y-m-d");

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO user (creation_date, email, name, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $created, $email, $name, $password);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            $start_date = date("Y-m-d");

            $stmt = $conn->prepare("INSERT INTO subscription_table (start_date, user_id) VALUES (?, ?)");
            $stmt->bind_param("si", $start_date, $user_id);

            if ($stmt->execute()) {
                $conn->commit();
                header("Location: index.php");
                exit();
            } else {
                throw new Exception("Error: " . $stmt->error);
            }
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }

    $stmt->close();
    $conn->close();
}
?>