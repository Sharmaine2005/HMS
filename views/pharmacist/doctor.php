<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pharmacist') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../config/db.php');

// Get doctor list
$doctors = $conn->query("SELECT * FROM doctor");
include('../../includes/pharmacist_sidebar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor List (Read-Only)</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Doctor List (Read-Only)</h2>

    <!-- Doctor List -->
    <table border="1">
        <tr>
            <th>Doctor ID</th>
            <th>Doctor Name</th>
            <th>Specialization</th>
        </tr>
        <?php while ($row = $doctors->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['DoctorID'] ?></td>
            <td><?= $row['Name'] ?></td>
            <td><?= $row['Specialization'] ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
