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
        $isbn = $_POST['isbn'];
        $price = $_POST['price'];
        $author = $_POST['author'];
        $type = $_POST['type'];
        $image = $_POST['image'];
        $sql = "INSERT INTO books (title, isbn, price, author, type, image) VALUES ('$title', '$isbn', '$price', '$author', '$type', '$image')";
        $conn->query($sql);
    } elseif (isset($_POST['edit_book'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $isbn = $_POST['isbn'];
        $price = $_POST['price'];
        $author = $_POST['author'];
        $type = $_POST['type'];
        $image = $_POST['image'];
        $sql = "UPDATE books SET title='$title', isbn='$isbn', price='$price', author='$author', type='$type', image='$image' WHERE id=$id";
        $conn->query($sql);
    } elseif (isset($_POST['delete_book'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM books WHERE id=$id";
        $conn->query($sql);
    }
}

// Fetch books from the database
$sql = "SELECT * FROM book";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Bookstore</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1,
    h2 {
        text-align: center;
    }

    form {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 10px;
    }

    input[type="text"],
    input[type="number"] {
        width: 90%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    button {
        padding: 10px 20px;
        background-color: #5cb85c;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #4cae4c;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    img {
        max-width: 100%;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Admin Panel</h1>
        <h2>Manage Books</h2>

        <form method="post">
            <h3>Add Book</h3>
            <label>Title: <input type="text" name="title" required></label>
            <label>ISBN: <input type="text" name="isbn" required></label>
            <label>Price: <input type="number" name="price" required></label>
            <label>Author: <input type="text" name="author" required></label>
            <label>Type: <input type="text" name="type" required></label>
            <label>Image: <input type="text" name="image" required></label>
            <button type="submit" name="add_book">Add Book</button>
        </form>

        <h3>Books List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>ISBN</th>
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
                <td><?php echo $row['ISBN']; ?></td>
                <td><?php echo $row['Price']; ?></td>
                <td><?php echo $row['Author']; ?></td>
                <td><?php echo $row['Type']; ?></td>
                <td><img src="<?php echo $row['Image']; ?>" alt="Book Image"></td>
                <td>
                    <form method="post" style="display:inline; ">
                        <input type="text" name="title" value="<?php echo $row['BookTitle'] ?>" required>
                        <input type="text" name="isbn" value="<?php echo $row['ISBN']; ?>" required>
                        <input type="number" name="price" value="<?php echo $row['Price']; ?>" required>
                        <input type="text" name="author" value="<?php echo $row['Author']; ?>" required>
                        <input type="text" name="type" value="<?php echo $row['Type']; ?>" required>
                        <input type="text" name="image" value="<?php echo $row['Image']; ?>" required>
                        <button type="submit" name="edit_book">Edit</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['BookID']; ?>">
                        <button type="submit" name="delete_book">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>

<?php
$conn->close();
?>