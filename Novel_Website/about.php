<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/contactUs_0.css">
</head>

<body>
    <div class="navlist">
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="about.php">Contact Us</a>
    </div>
    <div class="contact-container">
        <h2>Contact Us</h2>
        <?php
        // Include the database connection file
        include 'database.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $subject = $_POST['subject'];
            $message = $_POST['message'];

            // Use the $conn variable from database.php
            $sql = "INSERT INTO contact_us (name, email, subject, message) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $subject, $message);

            if ($stmt->execute()) {
                echo '<p id="formMessage" class="success">Your message has been sent!</p>';
            } else {
                echo '<p id="formMessage" class="error">Error: ' . $stmt->error . '</p>';
            }

            $stmt->close();
        }
        ?>
        <form class="contact-form" id="contactForm" method="POST" action="">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required>

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" placeholder="Subject" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" placeholder="Your Message" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>
</body>

</html>
