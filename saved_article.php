<?php
include 'connection.php';
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['sourceName'], $_POST['title'], $_POST['description'], $_POST['url'], $_POST['urlToImage'])
        && !empty($_POST['sourceName']) && !empty($_POST['title']) && !empty($_POST['description'])
        && !empty($_POST['url']) && !empty($_POST['urlToImage'])) {
        $sourceName = $_POST['sourceName'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $url = $_POST['url'];
        $urlToImage = $_POST['urlToImage'];

        $stmt = $conn->prepare("INSERT INTO saved_articles (source_name, title, description, url, urlToImage) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $sourceName, $title, $description, $url, $urlToImage);

        if ($stmt->execute()) {
            header("Location: index.php?saved_articles=true");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        die("Please fill in all the fields before submitting.");
    }
} else {
    die("Invalid request method.");
}
?>
