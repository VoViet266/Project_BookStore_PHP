<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="style.css">
<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
echo '<header>';
echo '<blockquote>';
echo '<a href="index.php"><img src="image/logo.png"></a>';
echo '<form class="hf" action="index.php"><input class="hi" type="submit" name="submitButton" value="BackToHome"></form>';
echo '<form class="hf" action="logout.php"><input class="hi" type="submit" name="submitButton" value="Logout"></form>';
echo '<form class="hf" action="edituser.php"><input class="hi" type="submit" name="submitButton" value="Edit Profile"></form>';
echo '</blockquote>';
echo '</header>';

// Database connection
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "bookstore";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_book'])) {
        $title = $_POST['title'];
        $price = $_POST['price'];
        $author = $_POST['author'];
        $type = $_POST['type'];
        $image = $_POST['image'];
        $sql = "INSERT INTO book (BookTitle, Price, Author, Type, Image) VALUES ('$title', '$price', '$author', '$type', '$image')";
        $conn->query($sql);
    } elseif (isset($_POST['edit_book'])) {
        $id = $_POST['BookID'];
        $title = $_POST['title'];
        $price = $_POST['price'];
        $author = $_POST['author'];
        $type = $_POST['type'];
        $image = $_POST['image'];
        $sql = "UPDATE book SET BookTitle='$title', Price='$price', Author='$author', Type='$type', Image='$image' WHERE BookID=$id";
        $conn->query($sql);
    } elseif (isset($_POST['delete_book'])) {
        $id = $_POST['BookID'];
        $sql = "DELETE FROM book WHERE BookID='$id'";
        $conn->query($sql);
    }
}

// Fetch books from the database
$sql = "SELECT * FROM book";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">


<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #eef2f3;
        margin: 0;
        padding: 0;
        color: #333;
    }

    .container {
        width: 80%;
        margin: 50px auto;
        background-color: #ffffff;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h1,
    h2 {
        text-align: center;
        color: #444;
        margin-bottom: 20px;
    }

    form {
        margin-bottom: 30px;
    }

    h3 {
        color: #5cb85c;
        display: inline-block;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 8px;
        color: #555;
    }

    input[type="text"],
    input[type="number"] {
        width: calc(100% - 20px);
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="number"]:focus {
        border-color: #5cb85c;
        box-shadow: 0 0 5px rgba(92, 184, 92, 0.5);
        outline: none;
    }

    button {
        padding: 10px 20px;
        background-color: #5cb85c;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #4cae4c;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        background-color: #fff;
        border-radius: 5px;
        overflow: hidden;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 12px 15px;
        text-align: left;
    }

    th {
        text-align: center;
        background-color: #f9f9f9;
        font-weight: bold;
        color: #555;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    img {
        max-width: 100px;
        border-radius: 5px;
        display: block;
        margin: 0 auto;
    }

    form[style="display:inline;"] {
        margin: 5px 0;
    }
</style>

<div class="container">
    <h1 style="color: #FF6A6A;">Admin</h1>
    <h2>Quản Lý Sách</h2>

    <form method="post">
        <h3>Thêm sách</h3>
        <div style="padding-left: 40px;"></div>
        <label>Title <input type="text" name="title" required></label>
        <label>Price <input type="number" name="price" required></label>
        <label>Author <input type="text" name="author" required></label>
        <label>Type <input type="text" name="type" required></label>
        <label>Image <input type="text" name="image" required></label>
        <button type="submit" name="add_book">Add Book</button>
</div>
</form>

<h3>Danh sách</h3>
<div style="padding-left: 40px;">
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Price</th>
            <th>Author</th>
            <th>Type</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['BookID']; ?></td>
                <td><?php echo $row['BookTitle']; ?></td>
                <td><?php echo $row['Price']; ?></td>
                <td><?php echo $row['Author']; ?></td>
                <td><?php echo $row['Type']; ?></td>
                <td><img src="<?php echo $row['Image']; ?>" alt="Book Image"></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="text" name="title" value="<?php echo $row['BookTitle'] ?>" required>
                        <input type="number" name="price" value="<?php echo $row['Price']; ?>" required>
                        <input type="text" name="author" value="<?php echo $row['Author']; ?>" required>
                        <input type="text" name="type" value="<?php echo $row['Type']; ?>" required>
                        <input type="text" name="image" value="<?php echo $row['Image']; ?>" required>
                        <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                        <button type="submit" name="edit_book">Edit</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                        <button type="submit" name="delete_book">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</div>
<script>
    document.querySelectorAll('form button[name="delete_book"]').forEach(button => {
        button.addEventListener('click', (e) => {
            if (!confirm("Bạn có chắc chắn muốn xóa cuốn sách này?")) {
                e.preventDefault();
            }
        });
    });
</script>


<?php
$conn->close();
?>