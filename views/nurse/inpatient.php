<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../config/db.php');

// Get inpatients with patient info and vitals joined on PatientID
$inpatients = $conn->query("
    SELECT i.InpatientID, i.PatientID, i.LocationID, 
           p.Name AS PatientName, p.Sex,
           v.Temperature, v.BloodPressure, v.Pulse, v.NurseNotes
    FROM inpatients i
    JOIN patients p ON i.PatientID = p.PatientID
    LEFT JOIN patientvitals v ON i.PatientID = v.PatientID
");

if (isset($_POST['update_inpatient'])) {
    $inpatient_id = $_POST['inpatient_id'];
    $location = $_POST['location_id'];
    $temperature = $_POST['temperature'];
    $blood_pressure = $_POST['blood_pressure'];
    $pulse = $_POST['pulse'];
    $nurse_notes = $_POST['nurse_notes'];

    // Update LocationID in inpatients table
    $stmt = $conn->prepare("UPDATE inpatients SET LocationID = ? WHERE InpatientID = ?");
    $stmt->bind_param("si", $location, $inpatient_id);
    $stmt->execute();

    // Get PatientID for this inpatient
    $stmt2 = $conn->prepare("SELECT PatientID FROM inpatients WHERE InpatientID = ?");
    $stmt2->bind_param("i", $inpatient_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $patient = $res2->fetch_assoc();
    $patient_id = $patient['PatientID'];

    // Check if vitals exist for this PatientID
    $check = $conn->prepare("SELECT PatientVitalID FROM patientvitals WHERE PatientID = ?");
    $check->bind_param("i", $patient_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update vitals
        $stmt3 = $conn->prepare("UPDATE patientvitals SET Temperature = ?, BloodPressure = ?, Pulse = ?, NurseNotes = ? WHERE PatientID = ?");
        $stmt3->bind_param("ssssi", $temperature, $blood_pressure, $pulse, $nurse_notes, $patient_id);
        $stmt3->execute();
    } else {
        // Insert vitals
        $stmt3 = $conn->prepare("INSERT INTO patientvitals (PatientID, Temperature, BloodPressure, Pulse, NurseNotes) VALUES (?, ?, ?, ?, ?)");
        $stmt3->bind_param("issss", $patient_id, $temperature, $blood_pressure, $pulse, $nurse_notes);
        $stmt3->execute();
    }

    header("Location: inpatient.php");
    exit();
}

include('../../includes/nurse_header.php');
include('../../includes/nurse_sidebar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inpatient Management</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Inpatient Management</h2>

    <table border="1">
        <tr>
            <th>Inpatient ID</th>
            <th>Patient Name</th>
            <th>Temperature (°C)</th>
            <th>Blood Pressure</th>
            <th>Pulse (bpm)</th>
            <th>Nurse Notes</th>
            <th>Location</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $inpatients->fetch_assoc()) { ?>
        <tr>
            <form method="POST" style="margin: 0;">
                <td><?= $row['InpatientID'] ?></td>
                <td><?= htmlspecialchars($row['PatientName']) ?></td>
                <td><input type="text" name="temperature" value="<?= htmlspecialchars($row['Temperature']) ?>" placeholder="e.g. 36.6" required></td>
                <td><input type="text" name="blood_pressure" value="<?= htmlspecialchars($row['BloodPressure']) ?>" placeholder="e.g. 120/80" required></td>
                <td><input type="text" name="pulse" value="<?= htmlspecialchars($row['Pulse']) ?>" placeholder="e.g. 72" required></td>
                <td><textarea name="nurse_notes" placeholder="Additional notes..." required><?= htmlspecialchars($row['NurseNotes']) ?></textarea></td>

                <td>
                    <textarea name="location_id" required><?= htmlspecialchars($row['LocationID']) ?></textarea>
                    <input type="hidden" name="inpatient_id" value="<?= $row['InpatientID'] ?>">
                    <button type="submit" name="update_inpatient">Update</button>
                </td>
                <td>
                    <button class="view-btn" type="button"
                        onclick="openModal(
                            '<?= htmlspecialchars($row['PatientID']) ?>',
                            '<?= htmlspecialchars($row['PatientName']) ?>',
                            '<?= htmlspecialchars($row['Sex']) ?>',
                            'Temp: <?= htmlspecialchars($row['Temperature']) ?> °C | BP: <?= htmlspecialchars($row['BloodPressure']) ?> | Pulse: <?= htmlspecialchars($row['Pulse']) ?> bpm',
                            'Inpatient',
                            '<?= $_SESSION['username'] ?>'
                        )">
                        View Details
                    </button>
                </td>
            </form>
        </tr>
        <?php } ?>
    </table>
</div>

<!-- Modal for viewing patient -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="profile-img"><img src="../../assets/patient-icon.png" alt="Patient"></div>
        <div class="info-row"><strong>Patient ID:</strong> <span id="modalPatientID"></span></div>
        <div class="info-row"><strong>Name:</strong> <span id="modalName"></span></div>
        <div class="info-row"><strong>Gender:</strong> <span id="modalSex"></span></div>
        <div class="info-row"><strong>Vital Signs:</strong> <span id="modalVitals"></span></div>
        <div class="info-row"><strong>Type:</strong> <span id="modalType"></span></div>
        <div class="info-row"><strong>Assigned Nurse:</strong> <span id="modalNurse"></span></div>
    </div>
</div>

<script>
function openModal(id, name, sex, vitals, type, nurse) {
    document.getElementById('modalPatientID').innerText = id;
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalSex').innerText = sex;
    document.getElementById('modalVitals').innerText = vitals;
    document.getElementById('modalType').innerText = type;
    document.getElementById('modalNurse').innerText = nurse;
    document.getElementById('detailModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
}
</script>

<style>
/* Keep your existing styles here */
body {
    font-family: Arial, sans-serif;
    background-color: #ffffff;
}
.content {
    padding: 40px;
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
form input, form button, textarea {
    padding: 5px 10px;
    margin-top: 5px;
}
button.view-btn {
    background-color: #6f42c1;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    cursor: pointer;
}
button.view-btn:hover {
    background-color: #512da8;
}
.modal {
    position: fixed;
    z-index: 999;
    left: 0; top: 0;
    width: 100%; height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
    display: none;
    justify-content: center;
    align-items: center;
}
.modal-content {
    border: 2px solid purple;
    border-radius: 12px;
    padding: 40px;
    background-color: #fff;
    max-width: 500px;
    width: 90%;
    text-align: center;
    box-shadow: 0 0 12px rgba(0,0,0,0.05);
    position: relative;
}
.close {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 28px;
    font-weight: bold;
    color: #888;
    cursor: pointer;
}
.close:hover {
    color: #000;
}
.profile-img {
    width: 100px;
    height: 100px;
    margin: 0 auto 30px;
    border-radius: 50%;
    background-color: #f0f0f0;
    display: flex;
    justify-content: center;
    align-items: center;
}
.profile-img img {
    width: 60px;
    height: 60px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    margin: 12px 0;
    font-size: 16px;
    color: #555;
}
.info-row strong {
    font-weight: 600;
    color: #444;
}
</style>

</body>
</html>