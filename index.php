<!DOCTYPE html>
<html>
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>News</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<h1>News</h1>
<div class="category-filter">
    <a href="?category=tesla" class="<?= ($_GET['category'] ?? 'tesla') === 'tesla' ? 'active' : '' ?>">Tesla</a>
    <a href="?category=apple" class="<?= ($_GET['category'] ?? 'tesla') === 'apple' ? 'active' : '' ?>">Apple</a>
    <a href="?category=business" class="<?= ($_GET['category'] ?? 'tesla') === 'business' ? 'active' : '' ?>">Business</a>
    <a href="?category=technology" class="<?= ($_GET['category'] ?? 'tesla') === 'technology' ? 'active' : '' ?>">Technology</a>
    <a href='?saved_articles=true'>Mentett cikkek</a>
</div>

<div class="articles-container">
    <?php
    include 'connection.php';
    global $conn;
    $api_key = "958c0653bad443fca1885c230854413f";
    $category = $_GET['category'] ?? 'tesla';

    if (isset($_GET['saved_articles'])) {
        $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $articlesPerPage = 4;

        $query = "SELECT * FROM saved_articles";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $totalSavedArticles = mysqli_num_rows($result);
            $totalPages = ceil($totalSavedArticles / $articlesPerPage);

            $offset = ($currentPage - 1) * $articlesPerPage;
            $queryPage = "SELECT * FROM saved_articles LIMIT $offset, $articlesPerPage";
            $resultPage = mysqli_query($conn, $queryPage);

            echo "<div class='articles-container'>";
            while ($row = mysqli_fetch_assoc($resultPage)) {
                echo "<div class='article'>";
                echo "<img src='" . $row['urlToImage'] . "' alt='Article Image'>";
                echo "<h2>" . $row['title'] . "</h2>";
                echo "<p><strong>Kategória:</strong> " . $row['source_name'] . "</p>";
                echo "<p>" . $row['description'] . "</p>";
                echo "<a href='" . $row['url'] . "' target='_blank'>Tovább olvasom</a>";

                echo "<form action='delete_article.php' method='POST'>";
                echo "<input type='hidden' name='article_id' value='" . $row['id'] . "'>";
                echo "<button type='submit'>Törlés</button>";
                echo "</form>";

                echo "<form action='comments.php' method='POST'>";
                echo "<input type='hidden' name='saved_article_id' value='" . $row['id'] . "'>";
                echo "<input type='text' name='commenter_name' placeholder='Név' required>";
                echo "<input type='text' name='comment_text' placeholder='Hozzászólás' required></input>";
                echo "<button type='submit'>Küldés</button>";
                echo "</form>";


                if (isset($_GET['saved_articles']) && $_GET['saved_articles'] === 'true') {
                    $articleRating = '';

                    echo "<div class='rating' data-article-id='" . $row['id'] . "'>";
                    echo "<div class='stars' data-rating='0'>";

                    $articleId = $row['id'];
                    $fetchRatingQuery = "SELECT rating FROM article_ratings WHERE saved_article_id = $articleId";
                    $ratingResult = mysqli_query($conn, $fetchRatingQuery);

                    $totalRating = 0;
                    $totalVotes = 0;

                    if ($ratingResult && mysqli_num_rows($ratingResult) > 0) {
                        $totalRating = 0;
                        $totalVotes = 0;

                        while ($rowRating = mysqli_fetch_assoc($ratingResult)) {
                            $rating = (int)$rowRating['rating'];
                            $totalRating += $rating;
                            $totalVotes++;
                        }

                        $articleRating = $totalVotes > 0 ? round($totalRating / $totalVotes, 2) : 0;

                        for ($i = 1; $i <= 5; $i++) {
                            $starClass = $i <= $articleRating ? 'star selected' : 'star';
                            echo "<span class='$starClass' data-value='$i'>&#9733;</span>";
                        }
                    } else {
                        for ($i = 1; $i <= 5; $i++) {
                            echo "<span class='star' data-value='$i'>&#9733;</span>";
                        }
                    }

                    echo "</div>";
                    if ($totalVotes > 0) {
                        echo "<span class='rating-value'>Összértékelés: $articleRating</span>";
                    }

                    echo "</div>";
                }

                $queryComments = "SELECT * FROM comments WHERE saved_article_id = " . $row['id'];
                $resultComments = mysqli_query($conn, $queryComments);

                echo "<div class='comments-section'>";
                while ($commentRow = mysqli_fetch_assoc($resultComments)) {
                    echo "<div class='comment'>";
                    echo "<p><strong>" . $commentRow['commenter_name'] . ": </strong>" . $commentRow['comment_text'] . "</p>";
                    echo "</div>";
                }
                echo "</div>";

                echo "</div>";

            }
            echo "</div>";

            echo "<div class='pagination'>";
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = $i === $currentPage ? 'active' : '';
                echo "<a href='?saved_articles=true&page=$i' class='pagination-link $active'>$i</a>";
            }
            echo "</div>";
        } else {
            echo "<p>Nincsenek mentett cikkek.</p>";
        }
    } else {
        $endpoints = [
            'tesla' => "https://newsapi.org/v2/everything?q=tesla&from=2023-12-07&sortBy=publishedAt&apiKey=$api_key",
            'apple' => "https://newsapi.org/v2/everything?q=apple&from=2024-01-06&to=2024-01-06&sortBy=popularity&apiKey=$api_key",
            'business' => "https://newsapi.org/v2/top-headlines?country=us&category=business&apiKey=$api_key",
            'technology' => "https://newsapi.org/v2/top-headlines?sources=techcrunch&apiKey=$api_key"
        ];

        $url = $endpoints[$category] ?? $endpoints['tesla'];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: '."958c0653bad443fca1885c230854413f",
            'User-Agent: testing'
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $newsData = json_decode($response, true);
        if ($newsData && $newsData['status'] === 'ok') {
            $totalArticles = count($newsData['articles']);
            $articlesPerPage = 8;
            $totalPages = ceil($totalArticles / $articlesPerPage);

            $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($currentPage - 1) * $articlesPerPage;

            $articles = array_slice($newsData['articles'], $offset, $articlesPerPage);

            foreach ($articles as $index => $article) {
                if ($index % 4 === 0) {
                    echo "<div class='row'>";
                }
                echo "<div class='article'>";
                echo "<img src='" . $article['urlToImage'] . "' alt='Article Image'>";
                echo "<h2>" . $article['title'] . "</h2>";
                echo "<p><strong>Kategória:</strong> " . $article['source']['name'] . "</p>";
                echo "<p>" . $article['description'] . "</p>";
                echo "<a href='" . $article['url'] . "' target='_blank'>Tovább olvasom</a>";

                echo "<form method='POST' action='saved_article.php'>";
                echo "<input type='hidden' name='sourceName' value='" . $article['source']['name'] . "'>";
                echo "<input type='hidden' name='title' value='" . $article['title'] . "'>";
                echo "<input type='hidden' name='description' value='" . $article['description'] . "'>";
                echo "<input type='hidden' name='url' value='" . $article['url'] . "'>";
                echo "<input type='hidden' name='urlToImage' value='" . $article['urlToImage'] . "'>";
                echo "<button type='submit'>Mentés</button>";
                echo "</form>";

                echo "</div>";

                if (($index + 1) % 4 === 0 || ($index + 1) === count($articles)) {
                    echo "</div>";
                }

            }

            echo "<div class='pagination'>";
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = $i === $currentPage ? 'active' : '';
                echo "<a href='?category=$category&page=$i' class='pagination-link $active'>$i</a>";
            }
            echo "</div>";
        }
    }

    ?>

    <script>
        $(document).ready(function() {
            $('.stars .star').on('click', function() {
                const articleId = $(this).closest('.rating').data('article-id');
                const ratingValue = $(this).data('value');

                $(this).parent().find('.star').removeClass('selected');
                $(this).prevAll('.star').addBack().addClass('selected');

                let ratingDisplay = $(this).closest('.rating').find('.rating-value');
                if (ratingDisplay.length === 0) {
                    $(this).closest('.rating').append("<span class='rating-value'></span>");
                    ratingDisplay = $(this).closest('.rating').find('.rating-value');
                }
                ratingDisplay.text("Ön értékelése: " + ratingValue);

                $.ajax({
                    type: 'POST',
                    url: 'rating.php',
                    data: {
                        article_id: articleId,
                        rating: ratingValue
                    },
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

</div>

</body>
</html>
