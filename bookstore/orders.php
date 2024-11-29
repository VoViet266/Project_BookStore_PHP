<link rel="stylesheet" href="order.css">
<?php
session_start();

include 'header.php';
include 'connectDB.php';
try {
    if (isset($_SESSION['admin_logged_in']) && isset($_SESSION['id']) && !isset($_SESSION['customer_logged_in'])) {
        $sql = "SELECT o.OrderID, b.BookTitle, o.Quantity, o.TotalPrice, o.DatePurchase, o.Status, o.CustomerID
                FROM `Orders` o
                JOIN Book b ON o.BookID = b.BookID"
                ;
                
        $result = $conn->query($sql);
        echo "<h2>Đơn Hàng</h2>";

        if ($result->num_rows > 0) {
            echo '<table class="table-orders">';
            echo '<tr><th> Customer ID</th><th>Order ID</th><th>Book Title</th><th>Quantity</th><th>Total Price</th><th>Order Date</th><th>Status</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['CustomerID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['OrderID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['BookTitle']) . "</td>"; // Hiển thị tiêu đề sách
                echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
                echo "<td>" . number_format($row['TotalPrice'], 2) . " VNĐ</td>";
                echo "<td>" . htmlspecialchars($row['DatePurchase']) . "</td>";
                echo '<td>
                        <form method="POST" action="">
                         <input type="hidden" name="OrderID" value="' . $row['OrderID'] . '">
                              <input type="submit" name="submitButton" value="' . ($row['Status'] === '1' ? "Đã Xử Lý" : "Chưa Xử Lý") . '" style="background-color: ' . ($row['Status'] === '1' ? '#4CAF50' : '#FF0000') . '; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 10px;">
                        </form>
                </td>';
                echo "</tr>";
                if (isset($_POST['submitButton'])) {
                    $OrderID = $_POST['OrderID'];

                    $sql = "UPDATE `Order` SET Status = 1 WHERE OrderID = $OrderID";
                    $conn->query($sql);
                }
            }
            echo '</table>';
        } else {
            echo "<p>No orders found.</p>";
        }
    }

    if (!isset($_SESSION['admin_logged_in']) && isset($_SESSION['id']) && isset($_SESSION['customer_logged_in'])) {
        $userId = intval($_SESSION['id']);
        echo "<h2>Đơn Hàng Của Bạn</h2>";
        $stmt = $conn->prepare(
            "SELECT o.OrderID, o.DatePurchase, o.Quantity, o.TotalPrice, o.Status, b.BookTitle, c.CustomerName
             FROM `Orders` o
             JOIN Customer c ON o.CustomerID = c.CustomerID
             JOIN Book b ON o.BookID = b.BookID
             WHERE c.UserID = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<table class="table-orders">';
            echo '<tr><th>Order ID</th><th>Book Title</th><th>Quantity</th><th>Total Price</th><th>Order Date</th><th>Status</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['OrderID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['BookTitle']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
                echo "<td>" . number_format($row['TotalPrice'], 2) . " VNĐ</td>";
                echo "<td>" . htmlspecialchars($row['DatePurchase']) . "</td>";
                echo "<td>" . ($row['Status'] === '1' ? "Đã Xử Lý" : "Chưa Xử Lý") . "</td>";
                echo "</tr>";
            }
            echo '</table>';
        } else {
            echo "<p>không có dữ liệu</p>";
        }

        $stmt->close();
    }
} catch (Exception $e) {
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
} finally {
    $conn->close();
}
?>