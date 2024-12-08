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
include 'connectDB.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM book";
if (!empty($search)) {
    $sql .= " WHERE BookTitle LIKE ? OR Author LIKE ? OR BookID LIKE ? OR Type LIKE ?";
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

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $uploadDir = 'image/'; 
            $imageName = basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $image = $imagePath;
            } else {
                $_SESSION['message'] = ["type" => "error", "text" => "Không thể upload ảnh."];
                header("Location: admin.php");
                exit();
            }
        } else {
            $image = $_POST['current_image'];
        }

        $sql = "UPDATE book SET BookTitle=?, Price=?, Author=?, Type=?, Image=? WHERE BookID=?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssss", $title, $price, $author, $type, $image, $id);

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
    }

    if (isset($_POST['delete_book'])) {
        $bookID = $_POST['BookID'];

        // Xóa sách khỏi cơ sở dữ liệu
        $sql = "DELETE FROM book WHERE BookID=?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $bookID);

            if ($stmt->execute()) {
                $_SESSION['message'] = ["type" => "success", "text" => "Xóa sách thành công!"];
            } else {
                $_SESSION['message'] = ["type" => "error", "text" => "Lỗi khi xóa sách: {$stmt->error}"];
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = ["type" => "error", "text" => "Lỗi chuẩn bị câu lệnh SQL: {$conn->error}"];
        }

        header("Location: admin.php");
        exit();
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
                    <td><?php echo htmlspecialchars($row['BookID']); ?></td>
                    <td><?php echo htmlspecialchars($row['BookTitle']); ?></td>
                    <td><?php echo htmlspecialchars($row['Price']); ?></td>
                    <td><?php echo htmlspecialchars($row['Author']); ?></td>
                    <td><?php echo htmlspecialchars($row['Type']); ?></td>
                    <td><img class="img_td" src="image/<?php echo basename($row['Image']); ?>" alt="Book Image"></td>
                    <td>
                        <form method="post" class="inline-form" style="display: inline-block;" action="admin.php" enctype="multipart/form-data">
                            <input type="text" name="title" value="<?php echo htmlspecialchars($row['BookTitle']) ?>" required>
                            <input type="number" name="price" value="<?php echo htmlspecialchars($row['Price']); ?>" required>
                            <input type="text" name="author" value="<?php echo htmlspecialchars($row['Author']); ?>" required>
                            <input type="text" name="type" value="<?php echo htmlspecialchars($row['Type']); ?>" required>
                            <input type="file" name="image" accept="image/*">
                            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($row['Image']); ?>">
                            <input type="hidden" name="BookID" value="<?php echo htmlspecialchars($row['BookID']); ?>"> <br>
                            <button type="submit" name="edit_book">Cập nhật</button>
                        </form>
                        <form method="post" class="inline-btn" style="display: inline-block;" action="admin.php">
                            <input type="hidden" name="BookID" value="<?php echo htmlspecialchars($row['BookID']); ?>">
                            <button class="button-delete" type="submit" name="delete_book" onclick="return confirm('Bạn có chắc chắn muốn xóa cuốn sách này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<script>
    // Auto-hide alert after 2 seconds
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
