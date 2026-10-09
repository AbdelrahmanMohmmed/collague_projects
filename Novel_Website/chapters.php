<?php
session_start();
include("database.php");

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

// Get the novel_id from the URL
if (isset($_GET['novel_id'])) {
    $novel_id = $_GET['novel_id'];

    // Fetch the novel details
    $novel_query = "SELECT title, description, photo FROM Novels WHERE novel_id = ?";
    $stmt = mysqli_prepare($conn, $novel_query);
    mysqli_stmt_bind_param($stmt, "i", $novel_id);
    mysqli_stmt_execute($stmt);
    $novel_result = mysqli_stmt_get_result($stmt);
    $novel = mysqli_fetch_assoc($novel_result);
    mysqli_stmt_close($stmt);

    // Fetch chapters of the selected novel
    if (isset($_GET['chapter_id'])) {
        $chapter_id = $_GET['chapter_id'];  // Get chapter_id from URL
    } else {
        // Default to first chapter
        $chapter_id = 1;
    }

    $chapters_query = "SELECT chapter_number, title, content FROM Chapters WHERE novel_id = ? ORDER BY chapter_number ASC";
    $stmt = mysqli_prepare($conn, $chapters_query);
    mysqli_stmt_bind_param($stmt, "i", $novel_id);
    mysqli_stmt_execute($stmt);
    $chapters_result = mysqli_stmt_get_result($stmt);

    // Get all chapters to easily navigate between them
    $chapters = [];
    while ($chapter = mysqli_fetch_assoc($chapters_result)) {
        $chapters[] = $chapter;
    }
    mysqli_stmt_close($stmt);

    // Find the current chapter index
    $current_chapter_index = -1;
    foreach ($chapters as $index => $chapter) {
        if ($chapter['chapter_number'] == $chapter_id) {
            $current_chapter_index = $index;
            break;
        }
    }

    // Get the next and previous chapter IDs
    $previous_chapter_id = ($current_chapter_index > 0) ? $chapters[$current_chapter_index - 1]['chapter_number'] : null;
    $next_chapter_id = ($current_chapter_index < count($chapters) - 1) ? $chapters[$current_chapter_index + 1]['chapter_number'] : null;

} else {
    // Redirect if no novel_id is provided
    header("Location: index.php");
    exit;
}

// Handle Like Button Click
if (isset($_POST['like_chapter_id'])) {
    $chapter_id = $_POST['like_chapter_id'];
    $user_id = $_SESSION['user_id'];

    // Check if user already liked the chapter
    $like_check_query = "SELECT * FROM Likes WHERE chapter_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $like_check_query);
    mysqli_stmt_bind_param($stmt, "ii", $chapter_id, $user_id);
    mysqli_stmt_execute($stmt);
    $like_result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($like_result) == 0) {
        // Insert Like if not already liked
        $insert_like_query = "INSERT INTO Likes (chapter_id, user_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insert_like_query);
        mysqli_stmt_bind_param($stmt, "ii", $chapter_id, $user_id);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Handle Comment Submission
if (isset($_POST['comment_text']) && !empty($_POST['comment_text'])) {
    $comment_text = $_POST['comment_text'];
    $user_id = $_SESSION['user_id'];

    // Insert the comment into the Comments table
    $insert_comment_query = "INSERT INTO Comments (novel_id, chapter_id, user_id, content) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_comment_query);
    mysqli_stmt_bind_param($stmt, "iiis", $novel_id, $chapter_id, $user_id, $comment_text);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapters of <?php echo htmlspecialchars($novel['title']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="navlist">
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="about.php">About Us</a>
    </div>
    <main>
        <h2><?php echo htmlspecialchars($novel['title']); ?> - Chapters</h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($novel['description']); ?></p>
        <?php if ($novel['photo']): ?>
            <img src="<?php echo htmlspecialchars($novel['photo']); ?>" alt="Novel Cover" style="max-width: 200px; height: auto;">
            <?php endif; ?>

        <h3>Chapter <?php echo $chapter_id; ?>: <?php echo htmlspecialchars($chapters[$current_chapter_index]['title']); ?></h3>
        <p><strong>Content:</strong> <?php echo nl2br(htmlspecialchars($chapters[$current_chapter_index]['content'])); ?></p>

        <!-- Display Likes for the Chapter -->
        <p>
            <?php
            $like_query = "SELECT COUNT(*) AS likes_count FROM Likes WHERE chapter_id = ?";
            $stmt = mysqli_prepare($conn, $like_query);
            mysqli_stmt_bind_param($stmt, "i", $chapter_id);
            mysqli_stmt_execute($stmt);
            $like_result = mysqli_stmt_get_result($stmt);
            $like_data = mysqli_fetch_assoc($like_result);
            $likes_count = $like_data['likes_count'];
            ?>
            Likes: <?php echo $likes_count; ?>
        </p>

        <!-- Like Button -->
        <form action="" method="POST">
            <input type="hidden" name="like_chapter_id" value="<?php echo $chapter_id; ?>">
            <button type="submit">Like</button>
        </form>

        <!-- Display Comments for the Chapter -->
        <h5>Comments</h5>
        <?php
        $comments_query = "SELECT content, u.name FROM Comments c JOIN Users u ON c.user_id = u.user_id WHERE c.chapter_id = ? ORDER BY created_at DESC";
        $stmt = mysqli_prepare($conn, $comments_query);
        mysqli_stmt_bind_param($stmt, "i", $chapter_id);
        mysqli_stmt_execute($stmt);
        $comments_result = mysqli_stmt_get_result($stmt);
        while ($comment = mysqli_fetch_assoc($comments_result)): ?>
            <p><strong><?php echo htmlspecialchars($comment['name']); ?>:</strong> <?php echo htmlspecialchars($comment['content']); ?></p>
        <?php endwhile; ?>

        <!-- Comment Submission Form -->
        <form action="" method="POST">
            <textarea name="comment_text" placeholder="Add your comment..." required></textarea>
            <button type="submit">Submit Comment</button>
        </form>

        <!-- Chapter Navigation -->
        <div class="chapter-navigation">
            <?php if ($previous_chapter_id): ?>
                <a href="chapters.php?novel_id=<?php echo $novel_id; ?>&chapter_id=<?php echo $previous_chapter_id; ?>">Previous Chapter</a>
            <?php endif; ?>
            <?php if ($next_chapter_id): ?>
                <a href="chapters.php?novel_id=<?php echo $novel_id; ?>&chapter_id=<?php echo $next_chapter_id; ?>">Next Chapter</a>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <div>This Website is made by college students as a project for their Web Programming course<br>
            Contact Us<br>
            01228774305</div>
    </footer>
</body>
</html>
