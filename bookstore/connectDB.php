<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=bookstore', 'root', 'mysql');
if (!$pdo) {
    echo "Connection failed";
}
echo "Connected successfully";

?>