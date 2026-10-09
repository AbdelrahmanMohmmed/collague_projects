<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Students Information</title>
<style>
    header {
            background-color: #66cdaa;
            padding: 12px 0;
        }
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 20px;
        font-size: 21px; 
    }
    .container {
        max-width: 900px;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 110px rgba(0, 0, 0, 0.1);
    }
    h1 {
        text-align: center;
        color: #343a40;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
    }
    table th, table td {
        border: 2px solid #000000;
        padding: 17px;
        text-align: left;
    }
    table th {
        background-color: #007bff;
        color: #212529;
    }
    table tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .link {
        text-align: center;
        margin-top: 20px;
    }
    .link a {
        display: inline-block;
        padding: 12px 40px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 111px;
    }
    .link a:hover {
        background-color: #0056b3;
    }
</style>
</head>
<header>
        <h1>Students Information</h1>
</header>
<body>

    <div class="container">
        <h1></h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>CGPA</th>
            </tr>
            <?php
$connect = mysqli_connect(
    'db', # service name
    'php_docker', # username
    'password', # password
    'php_docker' # db table
);

$table_name = "students_info";

$query = "SELECT * FROM $table_name";

$response = mysqli_query($connect, $query);

while($i = mysqli_fetch_assoc($response))
{
    echo "<tr>";
    echo "<td>".$i['id']."</td>";
    echo "<td>".$i['name']."</td>";
    echo "<td>".$i['age']."</td>";
    echo "<td>".$i['CGPA']."</td>";
    echo "</tr>";
}
           ?>
        </table>
        <div class="link">
            <a href="index.php">Go to Home page</a>
        </div>
    </div>
</body>
</html>
