<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

$sql = "SELECT users.username, inventory.item_name, inventory.quantity, inventory.price
        FROM inventory
        JOIN users ON inventory.user_id = users.id
        ORDER BY users.username";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
<h2>Admin Dashboard</h2>
<table border="1" cellpadding="5">
<tr><th>User</th><th>Item</th><th>Quantity</th><th>Price</th></tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['username'] ?></td>
    <td><?= $row['item_name'] ?></td>
    <td><?= $row['quantity'] ?></td>
    <td><?= $row['price'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<br><a href="logout.php">Logout</a>
</body>
</html>
