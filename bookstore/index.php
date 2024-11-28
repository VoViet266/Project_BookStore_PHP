<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
</head>


<body>
    <?php
	session_start();
	$servername = "localhost";
	$username = "root";
	$password = "mysql";

	$conn = new mysqli($servername, $username, $password);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	if (isset($_POST['ac'])) {

		$sql = "USE bookstore";
		$conn->query($sql);

		$sql = "SELECT * FROM book WHERE BookID = '" . $_POST['ac'] . "'";
		$result = $conn->query($sql);

		while ($row = $result->fetch_assoc()) {
			$bookID = $row['BookID'];
			$quantity = $_POST['quantity'];
			$price = $row['Price'];
		}

		$sql = "INSERT INTO cart(BookID, Quantity, Price, TotalPrice) VALUES('" . $bookID . "', " . $quantity . ", " . $price . ", Price * Quantity)";
		$conn->query($sql);
	}

	if (isset($_POST['delc'])) {
		$sql = "USE bookstore";
		$conn->query($sql);

		$sql = "DELETE FROM cart";
		$conn->query($sql);
	}

	$sql = "USE bookstore";
	$conn->query($sql);

	$sql = "SELECT * FROM book";
	$result = $conn->query($sql);
	?>

    <?php
	if (isset($_SESSION['admin_logged_in'])) {
		echo '<header>';
		echo '<blockquote>';
		echo '<a href="index.php"><img src="image/logo.png"></a>';
		
		echo '<form class="hf" action="admin.php"><input class="hi" type="submit" name="submitButton" value="Admin"></form>';
		echo '<form class="hf" action="logout.php"><input class="hi" type="submit" name="submitButton" value="Logout"></form>';
		echo '<form class="hf" action="edituser.php"><input class="hi" type="submit" name="submitButton" value="Edit Profile"></form>';
		echo '</blockquote>';
		echo '</header>';

	}
	if (isset($_SESSION['id']) && !isset($_SESSION['admin_logged_in'])) {
		echo '<header>';
		echo '<blockquote>';
		echo '<a href="index.php">
	<img src="image/logo.png">
	</a>';
		echo '<form class="hf" action="logout.php">
	<input class="hi" type="submit" name="submitButton" value="Logout">
	</form>';
		echo '<form class="hf" action="edituser.php">
	<input class="hi" type="submit" name="submitButton" value="Edit Profile">
	</form>';
		echo '</blockquote>';
		echo '</header>';
	}

	if (!isset($_SESSION['id']) && !isset($_SESSION['admin_logged_in'])) {
		echo '<header>';
		echo '<blockquote>';
		echo '<a href="index.php"><img src="image/logo.png"></a>';
		echo '<form class="hf" action="Register.php"><input class="hi" type="submit" name="submitButton" value="Register"></form>';
		echo '<form class="hf" action="login.php"><input class="hi" type="submit" name="submitButton" value="Login"></form>';
		echo '</blockquote>';
		echo '</header>';
	}
	echo '<blockquote>';
	// hiển thị danh sách sách
	echo "<ul id='myTable' style='width:75%; float:left; padding: 0; border-right: 1px solid #ddd;'>";
	while ($row = $result->fetch_assoc()) {
		echo "<li style='list-style-type: none; margin: 10px; border: 1px solid #928d8d; border-radius: 10px; padding: 10px; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2); width: 220px; height: auto; display: inline-block; vertical-align: top;'>";
		echo '<img src="' . $row["Image"] . '" style="width: 100%; height: 250px; object-fit: cover; border-radius: 10px; display: block; margin-left: auto; margin-right: auto;">';
		echo '<div style="padding: 5px; height: 50px"><strong>Title: ' . $row["BookTitle"] . '</strong></div>';
		echo '<div style="padding: 5px;">Author: ' . $row["Author"] . '</div>';
		echo '<div style="padding: 5px;">Type: ' . $row["Type"] . '</div>';
		echo '<div style="padding: 5px; float: right;">' . $row["Price"] . 'VNĐ</div>';
		echo '<div style="padding: 5px;">';
		echo '<form action="" method="post">';
		echo 'Quantity: <input type="number" value="1" name="quantity" style="width: 20%; border-radius: 15px;"/><br>';
		echo '<input type="hidden" value="' . $row['BookID'] . '" name="ac"/>';
		echo '<input class="button" type="submit" value="Add to Cart"/>';
		echo '</form>';
		echo '</div>';
		echo "</li>";
	}
	echo "</ul>";


	// hiển thị giỏ hàng
	$sql = "SELECT cart.CartID, book.BookTitle, book.Image, cart.Price, cart.Quantity, cart.TotalPrice FROM book, cart WHERE book.BookID = cart.BookID;";
	$result = $conn->query($sql);

	echo "<div style='width: 20%; float: right; padding: 10px; border-radius: 15px; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);'>";
	echo "<h2 style='text-align: left;'><i class='fa fa-shopping-cart' style='font-size: 24px;'></i> Cart";
	echo "<form style='float: right;' action='' method='post'>";
	echo "<input type='hidden' name='delc'/>";
	echo "<input class='cbtn' type='submit' value='Empty Cart'>";
	echo "</form>";
	echo "</h2>";
	$total = 0;
	while ($row = $result->fetch_assoc()) {
		echo "<div style='border-bottom: 1px solid #ddd; padding: 10px 0;'>";
		echo "<strong>" . $row['BookTitle'] . "</strong><br>";
		echo "Price: " . $row['Price'] . " VNĐ<br>";
		echo "Quantity: " . $row['Quantity'] . "<br>";
		echo "Total Price: " . $row['TotalPrice'] . " VNĐ";
		echo "<form action='' method='post' style='float: right; '>";
		echo "<input type='hidden' name='delItem' value='" . $row['CartID'] . "'/>";
		echo "<button type='submit' class='cbtn' style='background-color: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;'>Remove</button>";

		echo "</form>";
		echo "</div>";
		$total += $row['TotalPrice'];
	}
	if (isset($_POST['delItem'])) {
		$sql = "USE bookstore";
		$conn->query($sql);

		$sql = "DELETE FROM cart WHERE CartID = '" . $_POST['delItem'] . "'";
		$conn->query($sql);
	}
	echo "<div style='text-align: right; background-color: #f2f2f2; padding: 10px; border-radius: 5px;'>";
	echo "<strong>Total: " . $total . " VNĐ</strong>";
	echo "<center>";
	echo "<form action='checkout.php' method='post'>";
	echo "<input class='button' type='submit' name='checkout' value='CHECKOUT'>";
	echo "</form>";
	echo "</center>";
	echo "</div>";
	echo "</div>";
	echo '</blockquote>';
	?>
</body>

</html>