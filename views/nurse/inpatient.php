<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/admin_header.php');
include('../../includes/nurse_sidebar.php');
include('../../config/db.php');


// Get inpatient data
$inpatients = $conn->query("SELECT * FROM inpatients");

if (isset($_POST['update_inpatient'])) {
    // Example: Update inpatient data (e.g., vitals)
    $inpatient_id = $_POST['inpatient_id'];
    $vital_signs = $_POST['vital_signs'];
    $progress_notes = $_POST['progress_notes'];
    $stmt = $conn->prepare("UPDATE inpatients SET VitalSigns = ?, ProgressNotes = ? WHERE InpatientID = ?");
    $stmt->bind_param("ssi", $vital_signs, $progress_notes, $inpatient_id);
    $stmt->execute();
    header("Location: inpatient.php"); // Refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inpatient Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Inpatient Management</h2>

    <!-- Inpatient List -->
    <table border="1">
        <tr>
            <th>Inpatient ID</th>
            <th>Name</th>
            <th>Vital Signs</th>
            <th>Progress Notes</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $inpatients->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['InpatientID'] ?></td>
            <td><?= $row['PatientName'] ?></td>
            <td><form method="POST">
                <input type="text" name="vital_signs" value="<?= $row['VitalSigns'] ?>" required>
                <input type="hidden" name="inpatient_id" value="<?= $row['InpatientID'] ?>">
                <button type="submit" name="update_inpatient">Update</button>
            </form></td>
            <td><form method="POST">
                <textarea name="progress_notes" required><?= $row['ProgressNotes'] ?></textarea>
                <button type="submit" name="update_inpatient">Update</button>
            </form></td>
            <td><a href="view_inpatient.php?id=<?= $row['InpatientID'] ?>">View Details</a></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
