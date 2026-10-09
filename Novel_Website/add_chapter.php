<?php
include("database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $novel_id = filter_input(INPUT_POST, 'novel_id', FILTER_VALIDATE_INT);
    $chapter_number = filter_input(INPUT_POST, 'chapter_number', FILTER_VALIDATE_INT);
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');

    // Validate required fields
    if (!$novel_id || !$chapter_number || empty($title) || empty($content)) {
        die("All fields are required.");
    }

    // Check if the novel exists
    $checkNovelQuery = $conn->prepare("SELECT * FROM Novels WHERE novel_id = ?");
    $checkNovelQuery->bind_param("i", $novel_id);
    $checkNovelQuery->execute();
    $result = $checkNovelQuery->get_result();

    if ($result->num_rows === 0) {
        die("Invalid novel ID. Please provide a valid novel.");
    }

    // Insert the new chapter
    $query = $conn->prepare("INSERT INTO Chapters (novel_id, chapter_number, title, content) VALUES (?, ?, ?, ?)");
    $query->bind_param("iiss", $novel_id, $chapter_number, $title, $content);

    if ($query->execute()) {
        // Redirect with success
        header("Location: profile.php?message=Chapter added successfully");
        exit();
    } else {
        echo "Error adding chapter: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Chapter</title>
    <link rel="stylesheet" href="css/add_chapter_0.css">
</head>
<body>
    <div class="navlist">
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="about.php">Contact Us</a>
    </div>
    <div class="form-container">
        <h2>Add Chapter</h2>
        <?php $novel_id = $_GET['novel_id']; ?>
        <form action="add_chapter.php" method="post" class="add-chapter-form">
            <input type="hidden" name="novel_id" value="<?= $novel_id; ?>">
            
            <label for="chapter_number">Chapter Number:</label>
            <input type="number" id="chapter_number" name="chapter_number" required><br>

            <label for="title">Chapter Title:</label>
            <input type="text" id="title" name="title" required><br>

            <label for="content">Content:</label>
            <textarea id="content" name="content" required></textarea><br>

            <button type="submit" class="submit-button">Add Chapter</button>
        </form>
    </div>
</body>
</html>