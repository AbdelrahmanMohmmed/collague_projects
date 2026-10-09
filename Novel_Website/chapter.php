<?php
session_start();
include("database.php");

// Fetch the chapter based on novel_id and chapter_id
$novel_id = isset($_GET['id']) ? $_GET['id'] : null;
$chapter_id = isset($_GET['chapter']) ? $_GET['chapter'] : null;

if (!$novel_id || !$chapter_id) {
    // Redirect if either novel_id or chapter_id is not provided
    header("Location: index.php");
    exit;
}

$novel_query = "SELECT title, author FROM Novels WHERE novel_id = ?";
$stmt = mysqli_prepare($conn, $novel_query);
mysqli_stmt_bind_param($stmt, "i", $novel_id);
mysqli_stmt_execute($stmt);
$novel_result = mysqli_stmt_get_result($stmt);
$novel = mysqli_fetch_assoc($novel_result);
mysqli_stmt_close($stmt);

// Fetch chapter details
$chapter_query = "SELECT title, content FROM Chapters WHERE novel_id = ? AND chapter_number = ?";
$stmt = mysqli_prepare($conn, $chapter_query);
mysqli_stmt_bind_param($stmt, "ii", $novel_id, $chapter_id);
mysqli_stmt_execute($stmt);
$chapter_result = mysqli_stmt_get_result($stmt);
$chapter = mysqli_fetch_assoc($chapter_result);
mysqli_stmt_close($stmt);

// Get the next and previous chapter numbers
$next_chapter_query = "SELECT chapter_number FROM Chapters WHERE novel_id = ? AND chapter_number > ? ORDER BY chapter_number ASC LIMIT 1";
$prev_chapter_query = "SELECT chapter_number FROM Chapters WHERE novel_id = ? AND chapter_number < ? ORDER BY chapter_number DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $next_chapter_query);
mysqli_stmt_bind_param($stmt, "ii", $novel_id, $chapter_id);
mysqli_stmt_execute($stmt);
$next_chapter_result = mysqli_stmt_get_result($stmt);
$next_chapter = mysqli_fetch_assoc($next_chapter_result);
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, $prev_chapter_query);
mysqli_stmt_bind_param($stmt, "ii", $novel_id, $chapter_id);
mysqli_stmt_execute($stmt);
$prev_chapter_result = mysqli_stmt_get_result($stmt);
$prev_chapter = mysqli_fetch_assoc($prev_chapter_result);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapter: <?php echo htmlspecialchars($chapter['title']); ?></title>
    <link rel="stylesheet" href="css/style_0.css">
</head>
<body>
    <!-- <style>body{background-color: black;}</style> -->

    <div class="navlist">
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="about.php">Contact Us</a>
    </div>
    <main>
        <h1><?php echo htmlspecialchars($novel['title']); ?></h1>
        <h3>Chapter <?php echo $chapter_id; ?>: <?php echo htmlspecialchars($chapter['title']); ?></h3>
        <p><strong>Content:</strong> <?php echo nl2br(htmlspecialchars($chapter['content'])); ?></p>
        <div class="navigation">
            <?php if ($prev_chapter): ?>
                <button id="prev-chapter"> <a href="chapter.php?id=<?php echo $novel_id; ?>&chapter=<?php echo $prev_chapter['chapter_number']; ?>">Previous Chapter</a></button>

            <?php endif; ?>
            <?php if ($next_chapter): ?>
                <button id="next-chapter"><a href="chapter.php?id=<?php echo $novel_id; ?>&chapter=<?php echo $next_chapter['chapter_number']; ?>">Next Chapter</a></button>

            <?php endif; ?>
        </div>
    </main>
    <footer>
        <div>This Website is made by College students as a project for their Web Programming course<br>
            Contact Us<br>
            01228774305</div>
    </footer>
</body>
</html>

<?php
mysqli_free_result($chapter_result);
mysqli_free_result($next_chapter_result);
mysqli_free_result($prev_chapter_result);
mysqli_free_result($novel_result);
?>
