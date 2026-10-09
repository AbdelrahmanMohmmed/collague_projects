<?php
session_start();
include("database.php");

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get the novel_id from the query parameter
if (!isset($_GET['novel_id']) || empty($_GET['novel_id'])) {
    die("Novel ID is required.");
}

$novel_id = $_GET['novel_id'];

// Fetch novel details
$novel_query = "SELECT title, description FROM Novels WHERE novel_id = ?";
$stmt = mysqli_prepare($conn, $novel_query);

if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $novel_id);
mysqli_stmt_execute($stmt);
$novel_result = mysqli_stmt_get_result($stmt);
$novel = mysqli_fetch_assoc($novel_result);

if (!$novel) {
    die("Novel not found.");
}

mysqli_stmt_close($stmt);

// Fetch all chapters of the novel
$chapters_query = "SELECT chapter_number, title, content FROM Chapters WHERE novel_id = ? ORDER BY chapter_number ASC";
$stmt = mysqli_prepare($conn, $chapters_query);

if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $novel_id);
mysqli_stmt_execute($stmt);
$chapters_result = mysqli_stmt_get_result($stmt);

$chapters = [];
while ($row = mysqli_fetch_assoc($chapters_result)) {
    $chapters[] = $row;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapters of <?php echo htmlspecialchars($novel['title']); ?></title>
    <link rel="stylesheet" href="css/view_chapters.css">

</head>

<body>
    <h1>Chapters of "<?php echo htmlspecialchars($novel['title']); ?>"</h1>
    <p><?php echo htmlspecialchars($novel['description']); ?></p>
    <a href="profile.php">Back to Profile</a>

    <?php if (empty($chapters)) : ?>
        <p>No chapters have been added to this novel yet.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($chapters as $chapter) : ?>
                <li>
                    <strong>Chapter <?php echo $chapter['chapter_number']; ?>:</strong>
                    <?php echo htmlspecialchars($chapter['title']); ?>
                    <p><?php echo nl2br(htmlspecialchars($chapter['content'])); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="add_chapter.php?novel_id=<?php echo $novel_id; ?>">Add New Chapter</a>
</body>

</html>
