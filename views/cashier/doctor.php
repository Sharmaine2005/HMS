<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/cashier_header.php');
include('../../includes/cashier_sidebar.php');
include('../../config/db.php');


$doctors = $conn->query("SELECT DoctorID, DoctorName, DoctorFee FROM doctor");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctors (Read-Only)</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<div class="content">
    <h2>Doctors List (Read-Only)</h2>
    <table border="1">
        <tr>
            <th>ID</th><th>Name</th><th>Fee</th>
        </tr>
        <?php while ($row = $doctors->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['DoctorID'] ?></td>
                <td><?= $row['DoctorName'] ?></td>
                <td>₱<?= $row['DoctorFee'] ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>