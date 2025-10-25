<?php
session_start();
include('db_connect.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Get current user ID
$user_id = $_SESSION['user_id'];

// ADD item (link item to logged-in user)
if (isset($_POST['add'])) {
    $name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "INSERT INTO inventory (item_name, quantity, price, user_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidi", $name, $quantity, $price, $user_id);
    $stmt->execute();
}

// UPDATE item (only if it belongs to this user)
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "UPDATE inventory SET item_name=?, quantity=?, price=? 
            WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidii", $name, $quantity, $price, $id, $user_id);
    $stmt->execute();
}

// DELETE item (only if it belongs to this user)
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM inventory WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}

// Fetch only the current user's items
$sql = "SELECT * FROM inventory WHERE user_id = ? ORDER BY id ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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
    <h3>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>

    <div class="container">
        <!-- Add Item Form -->
        <form method="post" class="form-inline">
            <input type="text" name="item_name" placeholder="Item Name" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <button type="submit" name="add" class="add-btn">Add Item</button>
        </form>

        <!-- Inventory Table -->
        <table>
            <tr>
                <th>Sr. No</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price (₹)</th>
                <th>Actions</th>
            </tr>
            <?php
            $serial = 1;
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <form method="post">
                    <td><?= $serial ?></td>
                    <td><input type="text" name="item_name" value="<?= htmlspecialchars($row['item_name']) ?>"></td>
                    <td><input type="number" name="quantity" value="<?= $row['quantity'] ?>"></td>
                    <td><input type="number" step="0.01" name="price" value="<?= $row['price'] ?>"></td>
                    <td>
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="update" class="update-btn">Update</button>
                        <button type="submit" name="delete" class="delete-btn">Delete</button>
                    </td>
                </form>
            </tr>
            <?php 
            $serial++;
            endwhile;
            ?>
        </table>
    </div>

    <!-- Logout -->
    <form method="get" action="index.php">
        <button type="submit" name="logout" id="logout">Logout</button>
    </form>

    <?php
    // Handle logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: login.html");
        exit();
    }
    ?>
</body>
</html>
