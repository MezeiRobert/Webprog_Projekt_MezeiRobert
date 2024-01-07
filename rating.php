<?php
include 'connection.php';
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['article_id'], $_POST['rating'])) {
        $savedArticleId = $_POST['article_id'];
        $rating = $_POST['rating'];

        $checkArticle = "SELECT * FROM saved_articles WHERE id = '$savedArticleId'";
        $checkResult = mysqli_query($conn, $checkArticle);

        if (mysqli_num_rows($checkResult) > 0) {
            $insertRating = "INSERT INTO article_ratings (saved_article_id, rating) 
                                  VALUES ('$savedArticleId', '$rating') 
                                  ON DUPLICATE KEY UPDATE rating = VALUES(rating)";

            $result = mysqli_query($conn, $insertRating);

            if ($result) {
                echo "Rating saved successfully!";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "The article does not exist or is not saved.";
        }
    } else {
        echo "Missing data for rating.";
    }
} else {
    echo "Invalid request method.";
}
?>
