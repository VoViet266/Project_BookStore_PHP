<link rel="stylesheet" href="admin.css">
<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
include 'header.php';
include 'connectDB.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_book'])) {
        // $id = $_POST['bookid'];
        $title = $_POST['title'];
        $price = $_POST['price'];
        $author = $_POST['author'];
        $type = $_POST['type'];
        $image = $_POST['image'];

        $sql = "INSERT INTO book(BookTitle, Price, Author, Type, Image) VALUES ('$title', '$price', '$author', '$type', '$image')";
        $result = $conn->query($sql);
    }
}
?>
<form method="post">
    <h2>Thêm sách</h2>
    <div style="padding-left: 40px;"></div>
    <!-- <label for="">BookID<input type="text" name="bookid"></label> -->
    <label>Title <input type="text" name="title" required></label>
    <label>Price <input type="number" name="price" required></label>
    <label>Author <input type="text" name="author" required></label>
    <label>Type <input type="text" name="type" required></label>
    <label>Image <input type="text" name="image" required></label>
    <button type="submit" name="add_book">Add Book</button>
</form>