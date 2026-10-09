<?php
# before run it try run this first
# docker-php-ext-install mysqli
$connect = mysqli_connect(
    'db', # service name
    'php_docker', # username
    'password', # password
    'php_docker' # db table
);

$table_name = "students_info";

$query = "SELECT * FROM $table_name";

$response = mysqli_query($connect, $query);

echo "<strong>$table_name: </strong>";
while($i = mysqli_fetch_assoc($response))
{
    echo "<p>".$i['name']."</p>";
    echo "<p>".$i['age']."</p>";
    echo "<p>".$i['CGPA']."</p>";
    echo "<hr>";
}