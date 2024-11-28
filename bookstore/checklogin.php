<?php
session_start();
include "connectDB.php";
if (isset($_POST['username']) && isset($_POST['pwd'])) {
    $username = $_POST['username'];
    $pwd = $_POST['pwd'];
    // Truy vấn SQL chuẩn bị
    $sql = "SELECT * FROM Users WHERE UserName=? AND Password=?";
    $stmt = $conn->prepare($sql); // $conn là đối tượng kết nối mysqli
    $stmt->bind_param("ss", $username, $pwd); // Liên kết tham số
    $stmt->execute(); // Thực thi truy vấn
    $result = $stmt->get_result(); // Lấy kết quả

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Kiểm tra vai trò của người dùng
        if ($row['role'] == 'admin') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['id'] = $row['UserID'];
        } elseif ($row['role'] == 'customer') {
            $_SESSION['customer_logged_in'] = true;
            $_SESSION['id'] = $row['UserID'];
        }
        // Chuyển hướng tới trang chính
        header("Location:index.php");
        exit;
    } else {
        // Sai tên đăng nhập hoặc mật khẩu
        echo '<span style="color: red;">Login Fail</span>';
        header("Location:login.php?errcode=1");
        exit;
    }
}
?>