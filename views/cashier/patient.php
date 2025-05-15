<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/cashier_header.php');
include('../../includes/cashier_sidebar.php');
include('../../config/db.php');


$patients = $conn->query("SELECT * FROM patients");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patients (Read-Only)</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<div class="content">
    <h2>Patient Records (Read-Only)</h2>
    <table border="1">
        <tr>
            <th>ID</th><th>Name</th><th>DOB</th><th>Sex</th><th>Address</th><th>Contact</th>

        </tr>
        <?php while ($row = $patients->fetch_assoc()) { ?>
            <tr>
              <td><?= $row['PatientID'] ?></td>
                <td><?= $row['Name'] ?></td>
                <td><?= $row['DateOfBirth'] ?></td>
                <td><?= $row['Sex'] ?></td>
                <td><?= $row['Address'] ?></td>
                <td><?= $row['Contact'] ?></td>
                <tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
