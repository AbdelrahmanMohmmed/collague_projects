<?php
session_start();
include("database.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $author = $_POST['author'];
    $photo_url = $_POST['photo'];  // The URL from the form input (not a file upload)

    // Validate the URL (optional)
    if (!filter_var($photo_url, FILTER_VALIDATE_URL)) {
        echo "Invalid photo URL.";
        exit;
    }

    // Insert the novel details into the database
    $stmt = mysqli_prepare($conn, "INSERT INTO Novels (title, description, author, photo) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $title, $description, $author, $photo_url);
    $result = mysqli_stmt_execute($stmt);
    
    // Check if the insertion was successful
    if ($result) {
        echo "Novel added successfully!";
    } else {
        echo "Error adding novel.";
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    echo "Invalid request method.";
}
?>
