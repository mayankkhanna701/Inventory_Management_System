<?php
session_start();
include('db_connect.php');

// ADD item
if (isset($_POST['add'])) {
    $name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "INSERT INTO inventory (item_name, quantity, price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sid", $name, $quantity, $price);
    $stmt->execute();
}

// UPDATE item
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "UPDATE inventory SET item_name=?, quantity=?, price=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidi", $name, $quantity, $price, $id);
    $stmt->execute();
}

// DELETE item
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM inventory WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM inventory");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management System</title>
    <link rel="stylesheet" href="inventory.css">
</head>
<body>
    <h1>Inventory Management System</h1>
    <div class="container">
        <form method="post" class="form-inline">
            <input type="text" name="item_name" placeholder="Item Name" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <button type="submit" name="add" class="add-btn">Add Item</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price (₹)</th>
                <th>Actions</th>
            </tr>
            <?php
            $serial = 1;
            while ($row = $result->fetch_assoc()): ?>
            <tr>
                <form method="post">
                    <td><?= $serial ?></td>
                    <td><input type="text" name="item_name" value="<?= $row['item_name'] ?>"></td>
                    <td><input type="number" name="quantity" value="<?= $row['quantity'] ?>"></td>
                    <td><input type="number" step="0.01" name="price" value="<?= $row['price'] ?>"></td>
                    <td>
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="update" class="update-btn">Update</button>
                        <button type="submit" name="delete" class="delete-btn">Delete</button>
                    </td>
                </form>
            </tr>
            <?php $serial++; 
            endwhile; ?>
        </table>
    </div>
    <a href="logout.php">
    <input type="Submit" id="logout" Value="Logout">
    </a>
</body>
</html>
