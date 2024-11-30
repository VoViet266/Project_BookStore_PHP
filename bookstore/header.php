<link rel="stylesheet" href="header.css">
<?php

if (isset($_SESSION['admin_logged_in']) && !isset($_SESSION['customer_logged_in'])) {
?>
    <header>
        <a href="index.php" class="header-img"><img src="image/logo.png" alt="Logo"></a>
        <nav>
            <form class="hf" action="orders.php">
                <input class="hi" type="submit" name="submitButton" value="Xem đơn hàng">
            </form>

            <form class="hf" action="logout.php">
                <input class="hi" type="submit" name="submitButton" value="Đăng xuất">
            </form>
            <form class="hf" action="edituser.php">
                <input class="hi" type="submit" name="submitButton" value="Sửa thông tin cá nhân">
            </form>
            <form class="hf" action="admin.php">
                <input class="hi" type="submit" name="submitButton" value="Trang quản lý">
            </form>
        </nav>

    </header>

<?php
} elseif (isset($_SESSION['id']) && isset($_SESSION['customer_logged_in'])) {
?>
    <header>
        <a href="index.php" class="header-img"><img src="image/logo.png"></a>
        <nav>
            <form class="hf" action="orders.php">
                <input class="hi" type="submit" name="submitButton" value="Xem đơn hàng">
            </form>
            <form class="hf" action="logout.php"><input class="hi" type="submit" name="submitButton" value="Đăng xuất"></form>
            <form class="hf" action="edituser.php"><input class="hi" type="submit" name="submitButton" value="Sửa thông tin cá nhân">
            </form>
        </nav>
    </header>
<?php
} else {
?>
    <header>
        <a href="index.php" class="header-img"><img src="image/logo.png"></a>
        <nav>
            <form class="hf" action="Register.php"><input class="hi" type="submit" name="submitButton" value="Đăng ký">
            </form>
            <form class="hf" action="login.php"><input class="hi" type="submit" name="submitButton" value="Đăng nhập">
            </form>
        </nav>
    </header>
<?php
}
?>