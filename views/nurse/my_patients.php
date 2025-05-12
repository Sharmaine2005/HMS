<?php
session_start();

// Ensure the nurse is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_header.php');
include('../../includes/nurse_sidebar.php');
include('../../config/db.php');

// Get the nurse ID from the session
$nurseID = $_SESSION['NurseID'];  // This should hold the logged-in nurse's ID

// Fetch only patients assigned to the current nurse
$query_my_patients = "SELECT * FROM patients WHERE AssignedNurseID = ?";
$stmt_my_patients = $conn->prepare($query_my_patients);
$stmt_my_patients->bind_param("i", $nurseID);  // Bind nurse ID as an integer
$stmt_my_patients->execute();
$result_my_patients = $stmt_my_patients->get_result();
$my_patients = $result_my_patients->fetch_all(MYSQLI_ASSOC); // Get only the current nurse's patients

if (isset($_POST['update_patient'])) {
    // Update patient data (e.g., vitals)
    $patient_id = $_POST['patient_id'];
    $vital_signs = $_POST['vital_signs'];
    $stmt = $conn->prepare("UPDATE patients SET VitalSigns = ? WHERE PatientID = ?");
    $stmt->bind_param("si", $vital_signs, $patient_id);
    $stmt->execute();
    header("Location: my_patients.php"); // Refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Patients</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <style>
        .content {
            padding: 20px;
        }
        .button {
            margin-right: 15px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        form input, form button {
            padding: 5px 10px;
            margin-top: 5px;
        }
        .go-back-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .go-back-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

  <div class="content">
    <h2>Patient Management</h2>
    
    <!-- Buttons for All Patients and My Patients -->
    <div>
        <a href="patient.php" class="button">All Patients</a>
        <a href="my_patients.php" class="button">My Patients</a>
    </div>

    <!-- Patient List for the Current Nurse -->
    <h3>My Patients</h3>
    <table>
        <tr>
            <th>Patient ID</th>
            <th>Name</th>
            <th>Vital Signs</th>
            <th>Patient Type</th>
            <th>Action</th>
        </tr>
        <?php foreach ($my_patients as $row) { ?>
        <tr>
            <td><?= $row['PatientID'] ?></td>
            <td><?= $row['Name'] ?></td>
            <td>
                <form method="POST">
                    <input type="text" name="vital_signs" value="<?= isset($row['VitalSigns']) ? $row['VitalSigns'] : 'Not Available' ?>" required>
                    <input type="hidden" name="patient_id" value="<?= $row['PatientID'] ?>">
                    <button type="submit" name="update_patient">Update</button>
                </form>
            </td>
            <td>
                <?= isset($row['PatientType']) ? $row['PatientType'] : 'Unknown' ?>
            </td>
            <td><a href="view_patient.php?id=<?= $row['PatientID'] ?>">View Details</a></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
