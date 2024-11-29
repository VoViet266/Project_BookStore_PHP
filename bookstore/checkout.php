<link rel="stylesheet" href="checkout.css">

<body style="font-family:Arial; margin: 0 auto; background-color: #f2f2f2;">
    <header>

        <img src="image/logo.png">
        <input class="hi" style="float: right; margin: 2%;" type="button" name="cancel" value="Home"
            onClick="window.location='index.php';" />

    </header>
    <?php
    session_start();
    include 'connectDB.php';
    if (!isset($_SESSION['id'])) {
        header('Location: login.php');
        exit();
    }
    if (isset($_SESSION['id'])) {

        $sql = "SELECT CustomerID from customer WHERE UserID = {$_SESSION['id']}";
        $result = $conn->query($sql);
        echo $_SESSION['id'];
        $row = $result->fetch_assoc();
        $cID = $row['CustomerID'];
        // echo $cID; // 2
    

        $sql = "UPDATE cart SET CustomerID = '$cID' where 1";
        $conn->query($sql);

        $sql = "SELECT * FROM cart";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $sql = "INSERT INTO `orders`(CustomerID, BookID, DatePurchase, Quantity, TotalPrice, Status) 
			VALUES(" . $row['CustomerID'] . ", '" . $row['BookID']
                . "', CURRENT_TIME, " . $row['Quantity'] . ", " . $row['TotalPrice'] . ", 0)";
            $conn->query($sql);
        }
        $sql = "DELETE FROM cart";
        $conn->query($sql);

        $sql = "SELECT customer.CustomerName, customer.CustomerGender, customer.CustomerAddress, customer.CustomerEmail, customer.CustomerPhone, book.BookTitle, book.Price, book.Image, `orders`.`DatePurchase`, `orders`.`Quantity`, `orders`.`TotalPrice`
		FROM customer, book, `orders`
		WHERE `orders`.`CustomerID` = customer.CustomerID AND `orders`.`BookID` = book.BookID AND `orders`.`Status` = 0 AND `orders`.`CustomerID` = {$cID}";
        $result = $conn->query($sql);

        ?>


        <div class="container">
            <input class="button" style="float: right;" type="button" name="cancel" value="Continue Shopping"
                onClick="window.location='index.php';" />
            <h2 style="color: #000;">Order Successful</h2>
            <div class="sp" style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;">
                <?php
                $total = 0;
                while ($row = $result->fetch_assoc()) { ?>
                    <div class='product '>
                        <img src='<?php echo $row['Image']; ?>' alt='<?php echo $row['BookTitle']; ?> '>
                        <p style="margin: 0; font-weight: bold; ">Title: <?php echo $row['BookTitle']; ?></p>
                        <p style="margin: 3px 0;">Price: RM<?php echo $row['Price']; ?></p>
                        <p style="margin: 3px 0;">Quantity: <?php echo $row['Quantity']; ?></p>


                    </div>
                    <?php
                    $total += $row['TotalPrice'];
                }
                ?>
            </div>
        </div>
        <?php

        $sql = "SELECT * FROM customer WHERE CustomerID = $cID";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        ?>
        <table>
            <tr>

                <th>Order Summary</th>
                <th></th>
            </tr>
            <tr>
                <td>Name: </td>
                <td><?php echo empty($row['CustomerName']) ? "" : $row['CustomerName']; ?></td>
            </tr>
            <tr>
                <td>E-mail: </td>
                <td><?php echo empty($row['CustomerEmail']) ? "" : $row['CustomerEmail']; ?></td>
            </tr>
            <tr>
                <td>Mobile Number: </td>
                <td><?php echo empty($row['CustomerPhone']) ? "" : $row['CustomerPhone']; ?></td>
            </tr>
            <tr>
                <td>Gender: </td>
                <td><?php echo empty($row['CustomerGender']) ? "" : $row['CustomerGender']; ?></td>
            </tr>
            <tr>
                <td>Address: </td>
                <td><?php echo empty($row['CustomerAddress']) ? "" : $row['CustomerAddress']; ?></td>
            </tr>
            <?php
            $sql = "SELECT  `orders`.`DatePurchase`, `orders`.`TotalPrice`
		FROM customer, book, `orders`
		WHERE `orders`.`CustomerID` = customer.CustomerID AND `orders`.`BookID` = book.BookID AND  `orders`.`CustomerID` = {$cID}";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            ?>
            <tr>
                <td>Date: </td>
                <td><?php echo empty($row['DatePurchase']) ? "" : $row['DatePurchase']; ?></td>
            </tr>
            <tr>
                <td>Total Price: </td>
                <td>RM<?php echo $total; ?></td>
        </table>
        <?php
        $sql = "UPDATE `orders` SET Status = '1' WHERE CustomerID = " . $cID . "";
        $conn->query($sql);
    }




    ?>
    </div>
</body>