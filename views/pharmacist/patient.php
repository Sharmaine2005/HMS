<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pharmacist') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/pharmacist_sidebar.php');
include('../../config/db.php');

// Get patient list (read-only)
$patients = $conn->query("SELECT * FROM patients");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient List (Read-Only)</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Patient List (Read-Only)</h2>

    <!-- Patient List -->
    <table border="1">
        <tr>
            <th>Patient ID</th>
            <th>Patient Name</th>
            <th>Date of Birth</th> <!-- For debugging -->
            <th>Age</th>
            <th>Gender</th>
        </tr>
        <?php while ($row = $patients->fetch_assoc()) {
            $age = 'Unknown';
            if (!empty($row['DateOfBirth']) && $row['DateOfBirth'] != '0000-00-00') {
                try {
                    $dob = new DateTime($row['DateOfBirth']);
                    $age = (new DateTime())->diff($dob)->y;
                } catch (Exception $e) {
                    $age = 'Error';
                }
            }
        ?>
        <tr>
            <td><?= $row['PatientID'] ?></td>
            <td><?= $row['Name'] ?></td>
            <td><?= $row['DateOfBirth'] ?></td>
            <td><?= $age ?></td>
            <td><?= $row['Sex'] ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
