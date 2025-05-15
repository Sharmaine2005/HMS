<?php
session_start();

// Ensure the nurse is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}

// Include headers and sidebar
include('../../includes/admin_header.php');
include('../../includes/nurse_sidebar.php');
include('../../config/db.php');

// Get the nurse ID from the session
$nurseID = $_SESSION['NurseID'];  // This should hold the logged-in nurse's ID

// Fetch patients assigned to the current nurse (both inpatient and outpatient)
$query = "SELECT patients.*, 
                 IF(inpatients.PatientID IS NOT NULL, 'Inpatient', 
                    IF(outpatients.PatientID IS NOT NULL, 'Outpatient', 'Unknown')) AS PatientType
          FROM patients
          LEFT JOIN inpatients ON patients.PatientID = inpatients.PatientID
          LEFT JOIN outpatients ON patients.PatientID = outpatients.PatientID
          WHERE patients.AssignedNurseID = ? OR patients.AssignedNurseID IS NULL";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $nurseID);  // Bind nurse ID as an integer
$stmt->execute();
$result = $stmt->get_result();

// Check if there are patients assigned to the nurse
if ($result->num_rows > 0) {
    $patients = $result->fetch_all(MYSQLI_ASSOC); // Fetch all results
} else {
    echo "No patients found for this nurse.";
    exit();
}

// Fetch only patients assigned to the current nurse (for "My Patients" page)
$query_my_patients = "SELECT * FROM patients 
                      WHERE AssignedNurseID = ?";
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
    header("Location: patient.php"); // Refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <style>
        .content {
            padding: 20px;
        }

        .button {
            margin-right: 15px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007bff; /* Blue */
            color: white;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .button:hover {
            background-color: #0056b3; /* Darker Blue */
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
            background-color: #f8f9fa; /* Light Gray */
        }

        form input, form button {
            padding: 5px 10px;
            margin-top: 5px;
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

    <!-- Patient List for the Nurse -->
    <h3>All Patients (Inpatients and Outpatients)</h3>
    <table>
        <tr>
            <th>Patient ID</th>
            <th>Name</th>
            <th>Vital Signs</th>
            <th>Patient Type</th>
            <th>Action</th>
        </tr>
        <?php foreach ($patients as $row) { ?>
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
