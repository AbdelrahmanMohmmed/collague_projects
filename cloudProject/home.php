<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our PHP Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            color: #333;
            text-align: center;
        }
        header {
            background-color: #66cdaa;
            padding: 10px 0;
        }
        h1 {
            margin: 0;
            font-size: 2em;
            color: #fff;
        }
        main {
            padding: 20px;
        }
        p {
            font-size: 1.2em;
        }
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Our PHP Page</h1>
    </header>
    <main>
        <p><?php echo "Hello, visitor! Welcome to our PHP page."; ?></p>
        <p>Click <a href="index.php">here</a> to visit another PHP page.</p>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Company. All rights reserved.</p>
    </footer>
</body>
</html>
