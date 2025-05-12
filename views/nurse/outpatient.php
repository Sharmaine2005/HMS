<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/admin_header.php');
include('../../includes/nurse_sidebar.php');
include('../../config/db.php');


// Get outpatient data (view-only)
$outpatients = $conn->query("SELECT * FROM outpatients");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Outpatient Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Outpatient Management (View-Only)</h2>

    <!-- Outpatient List -->
    <table border="1">
        <tr>
            <th>Outpatient ID</th>
            <th>Name</th>
            <th>Appointment Date</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $outpatients->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['OutpatientID'] ?></td>
            <td><?= $row['PatientName'] ?></td>
            <td><?= $row['AppointmentDate'] ?></td>
            <td><?= $row['Status'] ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
