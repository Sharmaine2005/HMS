<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../config/db.php');

// Fetch appointments, patients, and doctors
$appointments = $conn->query("SELECT a.*, p.Name AS PatientName, d.DoctorName AS DoctorName 
                              FROM appointments a
                              JOIN patients p ON a.PatientID = p.PatientID
                              JOIN doctor d ON a.DoctorID = d.DoctorID");

// Fetch all doctors
$doctors = $conn->query("SELECT DoctorID, DoctorName FROM doctor");

// Fetch all patients
$patients = $conn->query("SELECT PatientID, Name FROM patients");

// Handle Add Appointment
if (isset($_POST['add_appointment'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    // Insert the new appointment into the database
    $stmt = $conn->prepare("INSERT INTO appointments (PatientID, DoctorID, AppointmentDate, AppointmentTime, Reason) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);
    $stmt->execute();
    header("Location: appointments.php");  // Redirect to the same page to display new appointment
    exit();
}

// Handle Edit Appointment
if (isset($_POST['edit_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("UPDATE appointments SET PatientID = ?, DoctorID = ?, AppointmentDate = ?, AppointmentTime = ?, Reason = ? WHERE AppointmentID = ?");
    $stmt->bind_param("iisssi", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason, $appointment_id);
    $stmt->execute();
    header("Location: appointments.php");
    exit();
}

// Handle Delete Appointment
if (isset($_GET['delete'])) {
    $appointment_id = $_GET['delete'];
    $conn->query("DELETE FROM appointments WHERE AppointmentID = $appointment_id");
    header("Location: appointments.php");
    exit();
}
include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Appointments</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Manage Appointments</h2>

    <!-- Add Appointment Form -->
    <h3>Add New Appointment</h3>
    <form method="POST">
        <label for="patient_id">Patient</label>
        <select name="patient_id" required>
            <option value="">Select Patient</option>
            <?php while ($p = $patients->fetch_assoc()) { ?>
                <option value="<?php echo $p['PatientID']; ?>"><?php echo $p['Name']; ?></option>
            <?php } ?>
        </select>

        <label for="doctor_id">Doctor</label>
        <select name="doctor_id" required>
            <option value="">Select Doctor</option>
            <?php while ($d = $doctors->fetch_assoc()) { ?>
                <option value="<?php echo $d['DoctorID']; ?>"><?php echo $d['DoctorName']; ?></option>
            <?php } ?>
        </select>

        <input type="date" name="appointment_date" required>
        <input type="time" name="appointment_time" required>
        <textarea name="reason" placeholder="Reason for appointment" required></textarea>
        <button type="submit" name="add_appointment">Add Appointment</button>
    </form>

    <!-- Edit Appointment Modal -->
    <div id="editModal" style="display:none;">
        <h3>Edit Appointment</h3>
        <form method="POST">
            <input type="hidden" name="appointment_id" id="appointment_id">
            
            <label for="edit_patient_id">Patient</label>
            <select name="patient_id" id="edit_patient_id" required>
                <option value="">Select Patient</option>
                <?php while ($p = $patients->fetch_assoc()) { ?>
                    <option value="<?php echo $p['PatientID']; ?>"><?php echo $p['Name']; ?></option>
                <?php } ?>
            </select>

            <label for="edit_doctor_id">Doctor</label>
            <select name="doctor_id" id="edit_doctor_id" required>
                <option value="">Select Doctor</option>
                <?php while ($d = $doctors->fetch_assoc()) { ?>
                    <option value="<?php echo $d['DoctorID']; ?>"><?php echo $d['DoctorName']; ?></option>
                <?php } ?>
            </select>

            <input type="date" name="appointment_date" id="edit_appointment_date" required>
            <input type="time" name="appointment_time" id="edit_appointment_time" required>
            <textarea name="reason" id="edit_reason" placeholder="Reason for appointment" required></textarea>
            <button type="submit" name="edit_appointment">Save Changes</button>
            <button type="button" onclick="closeEditForm()">Cancel</button>
        </form>
    </div>

    <!-- Appointments Table -->
    <h3>Existing Appointments</h3>
    <table border="1">
        <tr>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Reason</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $appointments->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['PatientName']; ?></td>
                <td><?php echo $row['DoctorName']; ?></td>
                <td><?php echo $row['AppointmentDate']; ?></td>
                <td><?php echo $row['AppointmentTime']; ?></td>
                <td><?php echo $row['Reason']; ?></td>
                <td>
                    <button onclick="openEditForm(<?php echo $row['AppointmentID']; ?>, <?php echo $row['PatientID']; ?>, <?php echo $row['DoctorID']; ?>, '<?php echo $row['AppointmentDate']; ?>', '<?php echo $row['AppointmentTime']; ?>', '<?php echo $row['Reason']; ?>')">Edit</button>
                    <a href="appointments.php?delete=<?php echo $row['AppointmentID']; ?>" onclick="return confirm('Are you sure you want to delete this appointment?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<script>
    function openEditForm(appointment_id, patient_id, doctor_id, appointment_date, appointment_time, reason) {
        document.getElementById('appointment_id').value = appointment_id;
        document.getElementById('edit_patient_id').value = patient_id;
        document.getElementById('edit_doctor_id').value = doctor_id;
        document.getElementById('edit_appointment_date').value = appointment_date;
        document.getElementById('edit_appointment_time').value = appointment_time;
        document.getElementById('edit_reason').value = reason;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>

</body>
</html>
