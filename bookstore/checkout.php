<link rel="stylesheet" href="checkout.css">

<body style="font-family:Arial; margin: 0 auto; background-color: #f2f2f2;">
    <header>
        <blockquote>
            <img src="image/logo.png">
            <input class="hi" style="float: right; margin: 2%;" type="button" name="cancel" value="Home"
                onClick="window.location='index.php';" />
        </blockquote>
    </header>
    <?php
    session_start();
    include 'connectDB.php';
    if (isset($_SESSION['id'])) {

        $sql = "SELECT CustomerID from customer WHERE UserID = " . $_SESSION['id'] . "";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $cID = $row['CustomerID'];
        }

        $sql = "UPDATE cart SET CustomerID = " . $cID . " WHERE 1";
        $conn->query($sql);

        $sql = "SELECT * FROM cart";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $sql = "INSERT INTO `order`(CustomerID, BookID, DatePurchase, Quantity, TotalPrice, Status) 
			VALUES(" . $row['CustomerID'] . ", '" . $row['BookID']
                . "', CURRENT_TIME, " . $row['Quantity'] . ", " . $row['TotalPrice'] . ", 0)";
            $conn->query($sql);
        }
        $sql = "DELETE FROM cart";
        $conn->query($sql);

        $sql = "SELECT customer.CustomerName, customer.CustomerGender, customer.CustomerAddress, customer.CustomerEmail, customer.CustomerPhone, book.BookTitle, book.Price, book.Image, `order`.`DatePurchase`, `order`.`Quantity`, `order`.`TotalPrice`
		FROM customer, book, `order`
		WHERE `order`.`CustomerID` = customer.CustomerID AND `order`.`BookID` = book.BookID AND `order`.`Status` = 0 AND `order`.`CustomerID` = {$cID}";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        ?>
        <div class="container">
            <blockquote>
                <input class="button" style="float: right;" type="button" name="cancel" value="Continue Shopping"
                    onClick="window.location='index.php';" />
                <h2 style="color: #000;">Order Successful</h2>
                <table style='width:60%'>
                    <tr>
                        <th>Order Summary</th>
                        <th></th>
                    </tr>
                    <tr>
                        <td>Name: </td>
                        <td><?php echo (empty($row['CustomerName']) ? "" : $row['CustomerName']); ?></td>
                    </tr>
                    <tr>
                        <td>E-mail: </td>
                        <td><?php echo (empty($row['CustomerEmail']) ? "" : $row['CustomerEmail']); ?></td>
                    </tr>
                    <tr>
                        <td>Mobile Number: </td>
                        <td><?php echo (empty($row['CustomerPhone']) ? "" : $row['CustomerPhone']); ?></td>
                    </tr>
                    <tr>
                        <td>Gender: </td>
                        <td><?php echo (empty($row['CustomerGender']) ? "" : $row['CustomerGender']); ?></td>
                    </tr>
                    <tr>
                        <td>Address: </td>
                        <td><?php echo (empty($row['CustomerAddress']) ? "" : $row['CustomerAddress']); ?></td>
                    </tr>
                    <tr>
                        <td>Date: </td>
                        <td><?php echo (empty($row['DatePurchase']) ? "" : $row['DatePurchase']); ?></td>
                    </tr>
                </table>
            </blockquote>
            <?php
            $sql = "SELECT customer.CustomerName,  customer.CustomerGender, customer.CustomerAddress, customer.CustomerEmail, customer.CustomerPhone, book.BookTitle, book.Price, book.Image, `order`.`DatePurchase`, `order`.`Quantity`, `order`.`TotalPrice`
		FROM customer, book, `order`
		WHERE `order`.`CustomerID` = customer.CustomerID AND `order`.`BookID` = book.BookID AND `order`.`Status` = 0 AND `order`.`CustomerID` = " . $cID . "";
            $result = $conn->query($sql);
            $total = 0;
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td style='border-top: 2px solid #ccc;'>
                        <img src="<?php echo $row["Image"]; ?>" width="20%">
                    </td>
                    <td style="border-top: 2px solid #ccc;">
                        <?php echo $row['BookTitle']; ?><br>RM<?php echo $row['Price']; ?><br>
                        Quantity: <?php echo $row['Quantity']; ?><br>
                    </td>
                </tr>
                <?php
                $total += $row['TotalPrice'];
            }
            ?>
            <tr>
                <td style='background-color: #ccc;'></td>
                <td style='text-align: right;background-color: #ccc;'>Total Price: <b>RM<?php echo $total; ?></b></td>
            </tr>
            </table>
        </div>
        <?php
        $sql = "UPDATE `order` SET Status = '1' WHERE CustomerID = " . $cID . "";
        $conn->query($sql);
    }

    $nameErr = $emailErr = $genderErr = $addressErr = $icErr = $contactErr = "";
    $name = $email = $gender = $address = $ic = $contact = "";
    $cID;

    if (isset($_POST['submitButton'])) {
        if (empty($_POST["name"])) {
            $nameErr = "Please enter your name";
        } else {
            $name = test_input($_POST["name"]);
            if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
                $nameErr = "Only letters and white space allowed";
            }
        }

        if (empty($_POST["email"])) {
            $emailErr = "Please enter your email address";
        } else {
            $email = test_input($_POST["email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
            }
        }

        if (empty($_POST["contact"])) {
            $contactErr = "Please enter your phone number";
        } else {
            $contact = test_input($_POST["contact"]);
            if (!preg_match("/^[0-9 -]*$/", $contact)) {
                $contactErr = "Please enter a valid phone number";
            }
        }
        if (empty($_POST["gender"])) {
            $genderErr = "Gender is required";
        } else {
            $gender = test_input($_POST["gender"]);
        }

        if (empty($_POST["address"])) {
            $addressErr = "Please enter your address";
        } else {
            $address = test_input($_POST["address"]);
        }

        if (empty($nameErr) && empty($emailErr) && empty($contactErr) && empty($genderErr) && empty($addressErr)) {
            $sql = "INSERT INTO customer(CustomerName, CustomerPhone, CustomerEmail, CustomerAddress, CustomerGender) 
					VALUES('$name', '$contact', '$email', '$address', '$gender')";
            $conn->query($sql);

            $cID = $conn->insert_id;

            $sql = "UPDATE cart SET CustomerID = $cID WHERE 1";
            $conn->query($sql);

            $sql = "SELECT * FROM cart";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $sql = "INSERT INTO `order`(CustomerID, BookID, DatePurchase, Quantity, TotalPrice, Status) 
						VALUES(" . $row['CustomerID'] . ", '" . $row['BookID'] . "', CURRENT_TIME, " . $row['Quantity'] . ", " . $row['TotalPrice'] . ", 1)";
                $conn->query($sql);
            }
            $sql = "DELETE FROM cart";
            $conn->query($sql);
        }
    }
    function test_input($data)
    {
        $data = trim($data);
        $data = stripcslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    if (!isset($_SESSION['id'])) {
        header('Location: login.php');
    }
    ?>
    </div>
</body>