<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include 'header.php';
include 'connectDB.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_book'])) {
    $title = trim($_POST['title']);
    $price = trim($_POST['price']);
    $author = trim($_POST['author']);
    $type = trim($_POST['type']);
    $target_dir = "image/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $image = basename($_FILES["image"]["name"]);

    if (empty($title) || empty($price) || empty($author) || empty($type) || empty($image)) {
        $message = "Vui lòng điền đầy đủ thông tin!";
    } else {

        $sql_max_id = "SELECT BookID FROM Book ORDER BY BookID DESC LIMIT 1";
        $result = $conn->query($sql_max_id);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_id = $row['BookID'];
            $last_number = (int)str_replace('B-', '', $last_id);
        } else {
            $last_number = 0;
        }

        $new_number = $last_number + 1;
        $new_book_id = 'B-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO Book (BookID, BookTitle, Price, Author, Type, Image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssdsss", $new_book_id, $title, $price, $author, $type, $image);

                if ($stmt->execute()) {
                    $_SESSION['message'] = ["type" => "success", "text" => "Thêm sách thành công!"];
                    header("Location: admin.php");
                    exit();
                } else {
                    $_SESSION['message'] = ["type" => "error", "text" => "Lỗi khi thêm sách: " . $stmt->error];
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = ["type" => "error", "text" => "Lỗi chuẩn bị câu lệnh SQL: " . $conn->error];
            }
        } else {
            $_SESSION['message'] = ["type" => "error", "text" => "Lỗi upload file ảnh"];
        }
    }
}

$conn->close();
?>

<link rel="stylesheet" href="admin.css">
<div class="container">
    <h2>Thêm Sách</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
            <?= htmlspecialchars($_SESSION['message']['text']) ?>
            <button class="alert-close" onclick="this.parentElement.style.display='none';">×</button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="post" action="add_book.php" enctype="multipart/form-data">
        <label>Tiêu đề: <input type="text" name="title" required></label><br>
        <label>Giá: <input type="number" name="price" step="0.01" required></label><br>
        <label>Tác giả: <input type="text" name="author" required></label><br>
        <label>Thể loại: <input type="text" name="type" required></label><br>
        <label>Hình ảnh: <input type="file" name="image" required></label><br>
        <div class="button-add-container">

            <button type="submit" name="add_book">Thêm sách</button>

            <a class="return-button" href="admin.php">Quay lại trang quản lý</a>

        </div>


    </form>

</div>

<script>
    window.onload = function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(function() {
                alert.style.display = 'none';
            }, 2000);
        }
    }
</script>