<link rel="stylesheet" href="style.css">
<?php
session_start();
$nameErr = $emailErr = $genderErr = $addressErr = $contactErr = $usernameErr = $passwordErr = "";
$name = $email = $gender = $address = $contact = $name = $password = "";
$cID;

include 'connectDB.php';

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
		$hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the password
	}

	$sql = "USE bookstore";
	$conn->query($sql);
	$sql = "INSERT INTO users (UserName, Password) VALUES('$name', '$hashed_password')"; // Store hashed password
	$conn->query($sql);

	$sql = "SELECT UserID FROM users WHERE UserName = '$name'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$id = $row['UserID'];
		}
		$sql = "INSERT INTO customer(CustomerName, CustomerPhone, CustomerEmail, CustomerAddress, CustomerGender, UserID) 
			VALUES('$name', '$contact', '$email', '$address', '$gender', $id)";
		$result = $conn->query($sql);
		header("Location:login.php");
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
}
function test_input($data)
{
	$data = trim($data);
	$data = stripcslashes($data);
	$data = htmlspecialchars($data);
	return $data;
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
            <form method="post" action="">
                <center>
                    <h1>Register</h1>
                </center>
                User Name:<br><input type="text" name="name" placeholder="User Name">
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $usernameErr; ?></span><br><br>

                Password:<br><input type="password" name="password" placeholder="Password">
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $passwordErr; ?></span><br><br>

                E-mail:<br><input type="text" name="email" placeholder="example@email.com">
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $emailErr; ?></span><br><br>

                Mobile Number:<br><input type="text" name="contact" placeholder="012-3456789">
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $contactErr; ?></span><br><br>

                <label>Gender:</label><br>
                <input type="radio" name="gender" <?php if (isset($gender) && $gender == "Male")
					echo "checked"; ?> value="Male">Male
                <input type="radio" name="gender" <?php if (isset($gender) && $gender == "Female")
					echo "checked"; ?> value="Female">Female
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $genderErr; ?></span><br><br>

                <label>Address:</label><br>
                <textarea name="address" cols="50" rows="5" placeholder="Address"></textarea>
                <span class="error" style="color: red; font-size: 0.8em;"><?php echo $addressErr; ?></span><br><br>

                <input class="button" type="submit" name="submitButton" value="Submit">
                <input class="button" type="button" name="cancel" value="Cancel"
                    onClick="window.location='login.php';" />
            </form>
        </div>
    </blockquote>
    </center>
</body>

</html>