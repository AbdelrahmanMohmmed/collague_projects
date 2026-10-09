<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database connection file
include("database.php");

// Fetch the novel based on ID from URL and validate it
$novel_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;

if (!$novel_id) {
    // Redirect if no valid novel_id is found in URL
    header("Location: index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Example user ID, change as needed

    if ($user_id) {
        // Check if the user has already liked the novel
        $check_like_query = "SELECT * FROM Likes WHERE novel_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $check_like_query);
        mysqli_stmt_bind_param($stmt, "ii", $novel_id, $user_id);
        mysqli_stmt_execute($stmt);
        $like_check_result = mysqli_stmt_get_result($stmt);
        $already_liked = mysqli_fetch_assoc($like_check_result);
        mysqli_stmt_close($stmt);

        if (!$already_liked) {
            // Insert a new like
            $insert_like_query = "INSERT INTO Likes (novel_id, user_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insert_like_query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ii", $novel_id, $user_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Refresh the page to update the like count
                header("Location: novel.php?id=$novel_id");
                exit;
            } else {
                die("Failed to insert like: " . mysqli_error($conn));
            }
        }
    } else {
        echo "<p>Please log in to like this novel.</p>";
    }
}

// Fetch novel details
$novel_query = "SELECT title, author, created_at AS date, description FROM Novels WHERE novel_id = ?";
$stmt = mysqli_prepare($conn, $novel_query);
if (!$stmt) {
    die("Query preparation failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $novel_id);
mysqli_stmt_execute($stmt);
$novel_result = mysqli_stmt_get_result($stmt);
$novel = mysqli_fetch_assoc($novel_result);
mysqli_stmt_close($stmt);

// Redirect if the novel is not found
if (!$novel) {
    header("Location: index.php");
    exit;
}

// Fetch chapters for the selected novel
$chapters_query = "SELECT chapter_number, title FROM Chapters WHERE novel_id = ? ORDER BY chapter_number ASC";
$stmt = mysqli_prepare($conn, $chapters_query);
if (!$stmt) {
    die("Query preparation failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $novel_id);
mysqli_stmt_execute($stmt);
$chapters_result = mysqli_stmt_get_result($stmt);

// Store chapters in an array
$chapters = [];
while ($chapter = mysqli_fetch_assoc($chapters_result)) {
    $chapters[] = $chapter;
}
mysqli_stmt_close($stmt);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Example user ID, change according to session logic
    $content = mysqli_real_escape_string($conn, $_POST['comment']);

    $comment_query = "INSERT INTO Comments (novel_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $comment_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iis", $novel_id, $user_id, $content);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        die("Failed to insert comment: " . mysqli_error($conn));
    }
}

// Fetch comments for the selected novel
$comments_query = "SELECT c.content, c.created_at, u.username FROM Comments c 
                   LEFT JOIN Users u ON c.user_id = u.user_id 
                   WHERE c.novel_id = ? ORDER BY c.created_at DESC";
$stmt = mysqli_prepare($conn, $comments_query);
mysqli_stmt_bind_param($stmt, "i", $novel_id);
mysqli_stmt_execute($stmt);
$comments_result = mysqli_stmt_get_result($stmt);
$comments = [];
while ($comment = mysqli_fetch_assoc($comments_result)) {
    $comments[] = $comment;
}
// Fetch like count
$like_query = "SELECT COUNT(*) AS like_count FROM Likes WHERE novel_id = ?";
$stmt = mysqli_prepare($conn, $like_query);
if (!$stmt) {
    die("Query preparation failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $novel_id);
mysqli_stmt_execute($stmt);
$like_result = mysqli_stmt_get_result($stmt);
$like_data = mysqli_fetch_assoc($like_result);
$like_count = $like_data['like_count'];

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($novel['title']); ?></title>
    <link rel="stylesheet" href="css/novel_0.css">
</head>
<body>
    <div class="navlist">
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="about.php">Contact Us</a>
    </div>
    
    <main>
        <h1><?php echo htmlspecialchars($novel['title']); ?></h1>
        <p><strong>Author:</strong> <?php echo htmlspecialchars($novel['author']); ?></p>
        <p><strong>Published Date:</strong> <?php echo htmlspecialchars($novel['date']); ?></p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($novel['description'])); ?></p>

        <h3>Chapters</h3>
        <ul id="chapters-list">
            <?php foreach ($chapters as $chapter): ?>
                <li>
                    <a href="chapter.php?id=<?php echo $novel_id; ?>&chapter=<?php echo $chapter['chapter_number']; ?>">
                        <?php echo htmlspecialchars($chapter['title']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Likes:</strong> <span id="like-count"><?php echo $like_count; ?></span></p>
<form method="POST" action="">
    <button type="submit" name="like">Like</button>
</form>

        <h3>Comments</h3>
        <form method="POST" action="">
            <textarea name="comment" placeholder="Write a comment..." required></textarea><br>
            <button type="submit">Add Comment</button>
        </form>

        <div id="comments-section">
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    <p><em>Posted on: <?php echo htmlspecialchars($comment['created_at']); ?></em></p>
                </div>
            <?php endforeach; ?>
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
// Free the result sets
mysqli_free_result($comments_result);
mysqli_free_result($chapters_result);
mysqli_free_result($novel_result);

// Close the database connection
mysqli_close($conn);
?>
