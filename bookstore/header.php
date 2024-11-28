<link rel="stylesheet" href="header.css">
<?php

if (isset($_SESSION['admin_logged_in']) && !isset($_SESSION['customer_logged_in'])) {
    ?>
<header>
    <a href="index.php" class="header-img"><img src="image/logo.png" alt="Logo"></a>
    <nav>
        <form class="hf" action="admin.php">
            <input class="hi" type="submit" name="submitButton" value="Admin">
        </form>
        <form class="hf" action="logout.php">
            <input class="hi" type="submit" name="submitButton" value="Logout">
        </form>
        <form class="hf" action="edituser.php">
            <input class="hi" type="submit" name="submitButton" value="Edit Profile">
        </form>
        <form class="hf" action="orders.php">
            <input class="hi" type="submit" name="submitButton" value="Xem đơn hàng">
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
        <form class="hf" action="logout.php"><input class="hi" type="submit" name="submitButton" value="Logout"></form>
        <form class="hf" action="edituser.php"><input class="hi" type="submit" name="submitButton" value="Edit Profile">
        </form>
    </nav>
</header>
<?php
} else {
    ?>
<header>
    <a href="index.php" class="header-img"><img src="image/logo.png"></a>
    <nav>
        <form class="hf" action="Register.php"><input class="hi" type="submit" name="submitButton" value="Register">
        </form>
        <form class="hf" action="login.php"><input class="hi" type="submit" name="submitButton" value="Login">
        </form>
    </nav>
</header>
<?php
}
?>