<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/cashier_header.php');
include('../../includes/cashier_sidebar.php');
include('../../config/db.php');


$meds = $conn->query("SELECT * FROM pharmacy");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy (Read-Only)</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<div class="content">
    <h2>Pharmacy Inventory (Read-Only)</h2>
    <table border="1">
        <tr>
            <th>ID</th><th>Name</th><th>Price</th><th>Quantity</th>
        </tr>
        <?php while ($row = $meds->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['PharmacyID'] ?></td>
                <td><?= $row['MedicineName'] ?></td>
                <td>₹<?= $row['Price'] ?></td>
                <td><?= $row['Quantity'] ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
