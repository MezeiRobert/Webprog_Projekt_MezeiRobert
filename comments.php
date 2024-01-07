<?php
include 'connection.php';
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['saved_article_id'], $_POST['commenter_name'], $_POST['comment_text'])) {
        $savedArticleId = $_POST['saved_article_id'];
        $commenterName = trim($_POST['commenter_name']);
        $commentText = trim($_POST['comment_text']);

        if (!empty($savedArticleId) && !empty($commenterName) && !empty($commentText)) {
            $insertComment = $conn->prepare("INSERT INTO comments (saved_article_id, commenter_name, comment_text) VALUES (?, ?, ?)");
            $insertComment->bind_param("iss", $savedArticleId, $commenterName, $commentText);

            $result = $insertComment->execute();

            if ($result) {
                header("Location: index.php?saved_articles=true");
                exit();
            } else {
                die("Error occurred while saving the comment: " . $insertComment->error);
            }
        } else {
            die("Please fill in all the fields before submitting.");
        }
    } else {
        die("Invalid form submission.");
    }
} else {
    die("Invalid request method.");
}

?>
