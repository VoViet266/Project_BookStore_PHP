<?php
$nameErr = $emailErr = $genderErr = $addressErr = $contactErr = $usernameErr = $passwordErr = "";
$name = $email = $gender = $address = $contact = $name = $password = "";
session_start();
include 'connectDB.php';

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
	$conn->query("UPDATE customer SET CustomerName = '$name', CustomerPhone = '$contact', CustomerEmail = '$email', CustomerAddress = '$address', CustomerGender = '$gender' WHERE UserID = {$_SESSION['id']}");
	header("Location: index.php");

}
function test_input($data)
{
	return htmlspecialchars(stripslashes(trim($data)));
}
?>
<html>
<link rel="stylesheet" href="style.css">

<body>
    <header>
        <blockquote>
            <a href="index.php"><img src="image/logo.png"></a>
        </blockquote>
    </header>
    <blockquote>
        <div class="container">
            <form method="post" action="edituser.php">
                <h1>Edit Profile:</h1>

                User Name:<br><input type="text" name="name" value="<?php echo $userName; ?>">
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $nameErr; ?></span><br><br>
                Password:<br><input type="password" name="password" value="<?php echo $userPassword; ?>">
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $passwordErr; ?></span><br><br>

                E-mail:<br><input type="text" name="email" value="<?php echo $custEmail; ?>">
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $emailErr; ?></span><br><br>

                Mobile Number:<br><input type="text" name="contact" value="<?php echo $custPhone; ?>">
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $contactErr; ?></span><br><br>

                <label>Gender:</label><br>
                <input type="radio" name="gender" <?php if ($custGender == "Male")
					echo "checked"; ?> value="Male">Male
                <input type="radio" name="gender" <?php if ($custGender == "Female")
					echo "checked"; ?> value="Female">Female
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $genderErr; ?></span><br><br>

                <label>Address:</label><br>
                <textarea name="address" cols="50" rows="5"><?php echo $custAddress; ?></textarea>
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $addressErr; ?></span><br><br>
                <input class="button" type="submit" name="submit" value="Submit" />
                <input class="button" type="button" name="cancel" value="Cancel"
                    onClick="window.location='index.php';" />
            </form>
        </div>

    </blockquote>
</body>

</html>