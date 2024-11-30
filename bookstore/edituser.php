<link rel="stylesheet" href="style.css">
<?php
$nameErr = $emailErr = $genderErr = $addressErr = $contactErr = $usernameErr = $passwordErr = "";
$name = $email = $gender = $address = $contact = $name = $password = "";
session_start();
include 'connectDB.php';
$message = "";
$sql = "SELECT users.UserName, users.Password, customer.CustomerName, customer.CustomerEmail, customer.CustomerPhone, customer.CustomerGender, customer.CustomerAddress
	FROM users, customer
	WHERE users.UserID = customer.UserID AND users.UserID = " . $_SESSION['id'];
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$userName = $row['UserName'];
	$userPassword = $row['Password'];
	$custName = $row['CustomerName'];
	$custEmail = $row['CustomerEmail'];
	$custPhone = $row['CustomerPhone'];
	$custGender = $row['CustomerGender'];
	$custAddress = $row['CustomerAddress'];

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (empty($_POST["name"])) {
		$nameErr = "Please enter your name";
	} else {
		$name = $_POST["name"];

		if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
			$nameErr = "Only letters and white space allowed";
		}
	}
	if (empty($_POST["email"])) {
		$emailErr = "Please enter your email address";
	} else {
		$email = $_POST["email"];
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$emailErr = "Invalid email format";
		}
	}

	if (empty($_POST["contact"])) {
		$contactErr = "Please enter your phone number";
	} else {
		$contact = $_POST["contact"];
		if (!preg_match("/^[0-9 -]*$/", $contact)) {
			$contactErr = "Please enter a valid phone number";
		}
	}
	if (empty($_POST["gender"])) {
		$genderErr = "Gender is required";
	} else {
		$gender = $_POST['gender'];
	}

	if (empty($_POST["address"])) {
		$addressErr = "Please enter your address";
	} else {
		$address = $_POST["address"];
	}

	if (empty($_POST["password"])) {
		$passwordErr = "Please enter your password";
	} else {
		$password = $_POST["password"];
	}

	$conn->query("UPDATE users SET UserName = '$name', Password = '$password' WHERE UserID = {$_SESSION['id']}");
	$result = $conn->query("UPDATE customer SET CustomerName = '$name', CustomerPhone = '$contact', CustomerEmail = '$email', CustomerAddress = '$address', CustomerGender = '$gender' WHERE UserID = {$_SESSION['id']}");

	if ($result) {
		$_SESSION['message'] = ["type" => "success", "text" => "Cập nhật thành công!!"];
		header("Location: edituser.php");
		exit();
	} else {
		$_SESSION['message'] = ["type" => "error", "text" => "Cập nhật thất bại!!!" . $conn->error];
	}


}
function test_input($data)
{
	return htmlspecialchars(stripslashes(trim($data)));
}
?>


<body>
	<header style="height: 13%;">
		<blockquote>
			<a href="index.php"><img style="margin: -0.3% 0 -0 -1.2%;" src="image/logo.png"></a>
		</blockquote>
	</header>
	<blockquote>
		<div class="container">
			<form method="post" action="edituser.php">

				<?php if (isset($_SESSION['message'])): ?>
					<div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
						<?= htmlspecialchars($_SESSION['message']['text']) ?>
						<button class="alert-close" onclick="this.parentElement.style.display='none';">×</button>
					</div>
					<?php unset($_SESSION['message']); ?>
				<?php endif; ?>
				<center>
					<h1>Chỉnh sửa thông tin</h1>
				</center>

				User Name:<br><input type="text" name="name" value="<?php echo $userName; ?>">
				<span class="error" style="color: red; font-size: 0.8em;"><?php echo $nameErr; ?></span><br><br>
				Mật khẩu:<br><input type="password" name="password" value="<?php echo $userPassword; ?>">
				<span class="error" style="color: red; font-size: 0.8em;"><?php echo $passwordErr; ?></span><br><br>

				E-mail:<br><input type="text" name="email" value="<?php echo $custEmail; ?>">
				<span class="error" style="color: red; font-size: 0.8em;"><?php echo $emailErr; ?></span><br><br>

				Số điện thoại:<br><input type="text" name="contact" value="<?php echo $custPhone; ?>">
				<span class="error" style="color: red; font-size: 0.8em;"><?php echo $contactErr; ?></span><br><br>

				<label>Giới tính:</label><br>
				<input type="radio" name="gender" <?php if ($custGender == "Male")
					echo "checked"; ?> value="Male">Male
				<input type="radio" name="gender" <?php if ($custGender == "Female")
					echo "checked"; ?>
					value="Female">Female
				<span class="error" style="color: red; font-size: 0.8em;"><?php echo $genderErr; ?></span><br><br>

				<label>Địa chỉ:</label><br>
				<textarea name="address" cols="50" rows="5"><?php echo $custAddress; ?></textarea>
				<span class="error" style="color: red; font-size: 0.8em;"><?php echo $addressErr; ?></span><br><br>
				<input class="button" type="submit" name="submit" value="Lưu" />
				<input class="button" type="button" name="cancel" value="Hủy"
					onClick="window.location='index.php';" />
			</form>
		</div>

	</blockquote>
</body>
<script>
	window.onload = function () {
		const alert = document.querySelector('.alert');
		if (alert) {
			setTimeout(function () {
				alert.style.display = 'none';
			}, 2000);
		}
	}
</script>