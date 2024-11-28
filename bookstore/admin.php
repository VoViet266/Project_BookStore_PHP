<link rel="stylesheet" href="admin.css">
<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
include 'header.php';
// Database connection
include 'connectDB.php';
// Handle form submissions
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
      
    } elseif (isset($_POST['edit_book'])) {
        $id = $_POST['BookID'];
        $title = $_POST['title'];
        $price = $_POST['price'];
        $author = $_POST['author'];
        $type = $_POST['type'];
        $image = $_POST['image'];
        $sql = "UPDATE book SET BookTitle='$title', Price='$price', Author='$author', Type='$type', Image='$image' WHERE BookID='$id'";
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

<div class="container">
    <h1 style="color: #FF6A6A;">Admin</h1>
    <h2>Quản Lý Sách</h2>

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

    <h1>Danh sách</h1>
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
                <td><img class="img_td" src="<?php echo $row['Image']; ?>" alt="Book Image"></td>
                <td>
                    <form method="post" class="inline-form" style="display: inline-block;">
                        <input type="text" name="title" value="<?php echo $row['BookTitle'] ?>" required>
                        <input type="number" name="price" value="<?php echo $row['Price']; ?>" required>
                        <input type="text" name="author" value="<?php echo $row['Author']; ?>" required>
                        <input type="text" name="type" value="<?php echo $row['Type']; ?>" required>
                        <input type="text" name="image" value="<?php echo $row['Image']; ?>" required>
                        <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                        <button type="submit" name="edit_book">Edit</button>
                    </form>
                    <form method="post" class="inline-btn" style="display: inline-block;">
                        <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                        <button type="submit" name="delete_book">Delete</button>

                    </form>

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