<?php
session_start();
include 'db_connect.php';

// Allow only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

// If admin clicked on a username
if (isset($_GET['user_id'])) {
    $selected_user_id = $_GET['user_id'];

    // Get user info
    $user_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $selected_user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();

    // Get that user’s inventory
    $stmt = $conn->prepare("SELECT * FROM inventory WHERE user_id = ?");
    $stmt->bind_param("i", $selected_user_id);
    $stmt->execute();
    $inventory = $stmt->get_result();
}
else {
    // Default: show all users
    $users = $conn->query("SELECT id, username FROM users WHERE role != 'admin' ORDER BY username");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #2f3640;
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 1.5em;
            letter-spacing: 1px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .container {
            width: 90%;
            max-width: 900px;
            background: white;
            margin: 40px auto;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h3 {
            text-align: center;
            color: #2f3640;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 10px;
        }

        th {
            background-color: #273c75;
            color: white;
            text-align: left;
            padding: 10px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f1f2f6;
        }

        a {
            color: #0984e3;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            background-color: #0984e3;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
        }

        .back-btn:hover {
            background-color: #74b9ff;
        }

        .logout {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #d63031;
            font-weight: bold;
            text-decoration: none;
        }

        .logout:hover {
            color: #ff7675;
        }

    </style>
</head>
<body>

<header>
    Admin Dashboard
</header>

<div class="container">
<?php if (!isset($_GET['user_id'])): ?>
    <h3>All Users</h3>
    <table>
        <tr>
            <th>#</th>
            <th>Username</th>
            <th>Action</th>
        </tr>
        <?php 
        $serial = 1;
        while ($row = $users->fetch_assoc()): 
        ?>
        <tr>
            <td><?= $serial++ ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><a href="admin.php?user_id=<?= $row['id'] ?>">View Inventory</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

<?php else: ?>
    <h3>Inventory of <?= htmlspecialchars($user['username']) ?></h3>
    <a href="admin.php" class="back-btn">← Back to User List</a>
    <table>
        <tr>
            <th>#</th>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Price (₹)</th>
        </tr>
        <?php 
        $serial = 1;
        while ($row = $inventory->fetch_assoc()):
        ?>
        <tr>
            <td><?= $serial++ ?></td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['price'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<a href="logout.php" class="logout">Logout</a>
</div>

</body>
</html>
