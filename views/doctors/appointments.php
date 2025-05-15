<?php
session_start();

include('../../includes/doctor_header.php');
include('../../includes/doctor_sidebar.php');
include('../../config/db.php');

// Handle Add Appointment
if (isset($_POST['add_appointment'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_SESSION['doctor_id']; // Assuming the doctor's ID is stored in session
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    // Use prepared statement to insert the appointment
    $stmt = $conn->prepare("INSERT INTO appointments (PatientID, DoctorID, AppointmentDate, AppointmentTime, Reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);
    $stmt->execute();
    header("Location: appointments.php");
    exit();
}

// Handle Edit Appointment
if (isset($_POST['edit_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $patient_id = $_POST['patient_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    // Use prepared statement to update the appointment
    $stmt = $conn->prepare("UPDATE appointments SET PatientID = ?, AppointmentDate = ?, AppointmentTime = ?, Reason = ? WHERE AppointmentID = ?");
    $stmt->bind_param("isssi", $patient_id, $appointment_date, $appointment_time, $reason, $appointment_id);
    $stmt->execute();
    header("Location: appointments.php");
    exit();
}

// Fetch appointments using a prepared statement
$stmt = $conn->prepare("SELECT * FROM appointments WHERE DoctorID = ?");
$stmt->bind_param("i", $_SESSION['doctor_id']);
$stmt->execute();
$appointments = $stmt->get_result();

// Fetch patients for the dropdown list
$patients = $conn->query("SELECT PatientID, Name FROM patients");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
        }

        .content {
            margin-left: 220px; /* sidebar width */
            padding: 20px;
        }

        h2, h3 {
            color: #9c335a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #9c335a;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group button {
            background-color: #9c335a;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #7a0154;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
        }

        .modal-content button {
            background-color: #28a745;
            border-radius: 5px;
        }

        .modal-content button.cancel {
            background-color: #dc3545;
        }
    </style>
</head>
<body>

<div class="content">
    <h2>Appointments</h2>

    <!-- Add Appointment Form -->
    <h3>Add Appointment</h3>
    <form method="POST">
        <div class="form-group">
            <select name="patient_id" required>
                <option value="">Select Patient</option>
                <?php while ($p = $patients->fetch_assoc()) { ?>
                    <option value="<?php echo $p['PatientID']; ?>"><?php echo $p['Name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <input type="date" name="appointment_date" required>
        </div>
        <div class="form-group">
            <input type="time" name="appointment_time" required>
        </div>
        <div class="form-group">
            <textarea name="reason" placeholder="Reason for appointment" required></textarea>
        </div>
        <div class="form-group">
            <button type="submit" name="add_appointment">Add Appointment</button>
        </div>
    </form>

    <h3>Existing Appointments</h3>
    <table>
        <tr>
            <th>Patient</th>
            <th>Date</th>
            <th>Time</th>
            <th>Reason</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $appointments->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['PatientID']; ?></td>
                <td><?php echo $row['AppointmentDate']; ?></td>
                <td><?php echo $row['AppointmentTime']; ?></td>
                <td><?php echo $row['Reason']; ?></td>
                <td>
                    <!-- Edit Appointment Button -->
                    <button onclick="openEditForm(<?php echo $row['AppointmentID']; ?>, <?php echo $row['PatientID']; ?>, '<?php echo $row['AppointmentDate']; ?>', '<?php echo $row['AppointmentTime']; ?>', '<?php echo $row['Reason']; ?>')">Edit</button>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Edit Appointment Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Appointment</h3>
            <form method="POST">
                <input type="hidden" name="appointment_id" id="appointment_id">
                <div class="form-group">
                    <select name="patient_id" id="edit_patient_id" required>
                        <option value="">Select Patient</option>
                        <?php while ($p = $patients->fetch_assoc()) { ?>
                            <option value="<?php echo $p['PatientID']; ?>"><?php echo $p['Name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="date" name="appointment_date" id="edit_appointment_date" required>
                </div>
                <div class="form-group">
                    <input type="time" name="appointment_time" id="edit_appointment_time" required>
                </div>
                <div class="form-group">
                    <textarea name="reason" id="edit_reason" required></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="edit_appointment">Save Changes</button>
                    <button type="button" class="cancel" onclick="closeEditForm()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to open the edit modal and pre-fill the form fields
    function openEditForm(appointment_id, patient_id, appointment_date, appointment_time, reason) {
        document.getElementById('appointment_id').value = appointment_id;
        document.getElementById('edit_patient_id').value = patient_id;
        document.getElementById('edit_appointment_date').value = appointment_date;
        document.getElementById('edit_appointment_time').value = appointment_time;
        document.getElementById('edit_reason').value = reason;
        document.getElementById('editModal').style.display = 'block';
    }

    // Function to close the edit modal
    function closeEditForm() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>

</body>
</html>
