<link rel="stylesheet" href="admin.css">
<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
$message = "";
include 'header.php';
// Database connection
include 'connectDB.php';
// Chức năng tìm kiếm
$search = isset($_GET['search']) ? $_GET['search'] : '';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM book";
if (!empty($search)) {
    $sql .= " WHERE BookTitle LIKE ? 
              OR Author LIKE ? 
              OR BookID LIKE ? 
              OR Type LIKE ?";
}
$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
}

$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit_book'])) {
        $id = $_POST['BookID'];
        $title = $_POST['title'];
        $price = $_POST['price'];
        $author = $_POST['author'];
        $type = $_POST['type'];
        $image = $_POST['image'];
        $sql = "UPDATE book SET BookTitle='$title', Price='$price', Author='$author', Type='$type', Image='$image' WHERE BookID=?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $id);

            if ($stmt->execute()) {
                $_SESSION['message'] = ["type" => "success", "text" => "Cập nhật sách thành công!"];
                header("Location: admin.php");
                exit();
            } else {
                $_SESSION['message'] = ["type" => "error", "text" => "Lỗi khi cập nhật sách: {$stmt->error}"];
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = ["type" => "error", "text" => "Lỗi chuẩn bị câu lệnh SQL: {$conn->error}"];
        }
    } elseif (isset($_POST['delete_book'])) {
        $id = $_POST['BookID'];
        $sql = "DELETE FROM book WHERE BookID=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $id);

            if ($stmt->execute()) {
                $_SESSION['message'] = ["type" => "success", "text" => "Xóa sách thành công!"];
                header("Location: admin.php");
                exit();
            } else {
                $_SESSION['message'] = ["type" => "error", "text" => "Lỗi khi xóa sách: {$stmt->error}"];
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = ["type" => "error", "text" => "Lỗi chuẩn bị câu lệnh SQL: {$conn->error}"];
        }
    }
}
?>
<div class="container">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
            <?= htmlspecialchars($_SESSION['message']['text']) ?>
            <button class="alert-close" onclick="this.parentElement.style.display='none';">×</button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <h1 style="color: #FF6A6A;">QUẢN LÝ SÁCH</h1>

    <hr>
    <br>
    <div class="add-return-container">
        <div><a class="button-addbook" href="add_book.php">Thêm sách mới</a></div>
        <div><a class="return-button-1" href="index.php">Quay lại trang chủ</a></div>
    </div>

    <form class="search-form" method="get" action="">
        <label style="margin: 30px 0 0 40px;" for="">Tìm kiếm sách: tìm theo mã sách, tên sách, tác giả và thể
            loại.</label>
        <input type="text" name="search" placeholder="Tìm kiếm sách" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Tìm kiếm</button>
    </form>
    <hr style="margin-bottom: 30px;">
    <div style="padding-left: 40px;">
        <table>
            <tr>
                <th>Mã sách</th>
                <th>Tiêu đề</th>
                <th>Giá</th>
                <th>Tác giả</th>
                <th>Thể loại</th>
                <th>Hình ảnh</th>
                <th>Hành động</th>
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
                        <form method="post" class="inline-form" style="display: inline-block;" action="admin.php">
                            <input type="text" name="title" value="<?php echo $row['BookTitle'] ?>" required>
                            <input type="number" name="price" value="<?php echo $row['Price']; ?>" required>
                            <input type="text" name="author" value="<?php echo $row['Author']; ?>" required>
                            <input type="text" name="type" value="<?php echo $row['Type']; ?>" required>
                            <input type="text" name="image" value="<?php echo $row['Image']; ?>" required>
                            <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                            <button type="submit" name="edit_book">Edit</button>
                        </form>
                        <form method="post" class="inline-btn" style="display: inline-block; " action="admin.php">
                            <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                            <button class="button-delete" type="submit" name="delete_book">Delete</button>
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


<!-- Ẩn thông báo trong 2 giây -->
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

<?php
$conn->close();
?>