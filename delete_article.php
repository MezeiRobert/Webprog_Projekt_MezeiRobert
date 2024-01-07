<?php
include 'connection.php';
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['article_id'])) {
        $articleId = $_POST['article_id'];

        $deleteRatings = "DELETE FROM article_ratings WHERE saved_article_id = '$articleId'";
        $resultRatings = mysqli_query($conn, $deleteRatings);

        if (!$resultRatings) {
            echo "Error deleting ratings: " . mysqli_error($conn);
            exit();
        }

        $deleteArticle = "DELETE FROM saved_articles WHERE id = '$articleId'";
        $resultArticle = mysqli_query($conn, $deleteArticle);

        if ($resultArticle) {
            header("Location: index.php?saved_articles=true");
            exit();
        } else {
            echo "Error deleting article: " . mysqli_error($conn);
        }
    } else {
        echo "Missing article ID for deletion.";
    }
} else {
    echo "Invalid request method.";
}
?>
