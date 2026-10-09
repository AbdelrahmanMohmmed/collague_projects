<?php
session_start();
include("database.php");

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Fetch user's novels and chapters
$novels_query = "SELECT Novels.novel_id, Novels.title, COUNT(Chapters.chapter_id) AS chapter_count 
                 FROM Novels 
                 LEFT JOIN Chapters ON Novels.novel_id = Chapters.novel_id 
                 WHERE Novels.author = ? 
                 GROUP BY Novels.novel_id";
$stmt = mysqli_prepare($conn, $novels_query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$novels_result = mysqli_stmt_get_result($stmt);

$novels = [];
while ($row = mysqli_fetch_assoc($novels_result)) {
    $novels[] = $row;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $username; ?>'s Profile</title>
    <link rel="stylesheet" href="css/profile_1.css">
    <script src="js/profile.js" defer></script>
</head>

<body>

    <!-- Navigation Bar -->
    <div class="navlist">
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="about.php">Contact Us</a>
        <a href="login.php">Logout</a>
    </div>

    <!-- User Info Section -->
    <section class="user-info">
        <img src="https://d28hgpri8am2if.cloudfront.net/book_images/onix/cvr9781524879761/the-great-gatsby-9781524879761_hr.jpg" alt="User Profile Picture" class="profile-picture">
        <h1 id="user-name"><?php echo htmlspecialchars($username); ?></h1>
        <p id="user-bio">Email: <?php echo htmlspecialchars($email); ?></p>
    </section>

    <!-- My Novels Section -->
    <section id="my-novel" class="novel-section">
        <h2>Your Novels</h2>
        <div class="novel-list" id="novel-list">
            <?php foreach ($novels as $novel) : ?>
                <div class="novel-item">
                    <strong><?php echo htmlspecialchars($novel['title']); ?></strong>
                    <p>Chapters: <?php echo $novel['chapter_count']; ?></p>
                    <a href="view_chapters.php?novel_id=<?php echo $novel['novel_id']; ?>">View Chapters</a> |
                    <a href="add_chapter.php?novel_id=<?php echo $novel['novel_id']; ?>">Add New Chapter</a>
                </div>
            <?php endforeach; ?>
        </div>
        <button id="add-novel-btn">Add Novel</button>
    </section>

    <!-- Add Novel Form Modal -->
    <div id="add-novel-form" class="modal hidden">
        <form action="insert_novel.php" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description"></textarea>

            <label for="photo">Photo URL:</label>
            <input type="text" name="photo" id="photo" placeholder="Paste image URL here" required>

            <!-- Hidden input for author (set from the session) -->
            <input type="hidden" name="author" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>">

            <button type="submit">Add Novel</button>
        </form>
    </div>

    <footer>
        <div>This Website is made by College students as a project for their Web Programming course.<br>
            Contact Us<br>
            01228774305</div>
    </footer>
    <script>// Example function to show the modal
function showModal() {
    document.getElementById('add-novel-form').classList.add('visible');
}

// Example function to hide the modal
function hideModal() {
    document.getElementById('add-novel-form').classList.remove('visible');
}

// Example: Add event listener to show the modal when the 'Add Novel' button is clicked
document.getElementById('add-novel-btn').addEventListener('click', showModal);

// Example: Add event listener to close the modal (if using a close button)
document.querySelector('#add-novel-form .close-btn').addEventListener('click', hideModal);
</script>
</body>

</html>
