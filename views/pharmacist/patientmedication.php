<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pharmacist') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/pharmacist_sidebar.php');
include('../../config/db.php');

// Get patient medications
$medications = $conn->query("SELECT pm.*, p.Name AS PatientName, d.DoctorName AS DoctorName 
                            FROM patientmedication pm
                            JOIN patients p ON pm.PatientID = p.PatientID
                            JOIN doctor d ON pm.DoctorID = d.DoctorID");


?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Medication</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Patient Medication (View-Only)</h2>

    <!-- Medication List -->
    <table border="1">
        <tr>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Medication</th>
            <th>Dosage</th>
            <th>Start Date</th>
            <th>End Date</th>
        </tr>
        <?php while ($row = $medications->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['PatientName'] ?></td>
            <td><?= $row['DoctorName'] ?></td>
            <td><?= $row['Medication'] ?></td>
            <td><?= $row['Dosage'] ?></td>
            <td><?= $row['StartDate'] ?></td>
            <td><?= $row['EndDate'] ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
