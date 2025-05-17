<?php
include('../../config/db.php');
session_start();

if (!isset($_SESSION['role_id'])) {
    echo "Access denied.";
    exit;
}

$loggedInNurseId = $_SESSION['role_id'];

$showModal = false;
$modalType = '';
$newPatientID = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $sex = $_POST['sex'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $patientType = $_POST['patient_type'];
    $assignedNurseId = $_POST['assigned_nurse_id'];

    $stmt = $conn->prepare("INSERT INTO patients (Name, DateOfBirth, Sex, Address, Contact, PatientType, AssignedNurseID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $name, $dob, $sex, $address, $contact, $patientType, $assignedNurseId);

    if ($stmt->execute()) {
        $newPatientID = $stmt->insert_id;
        $modalType = $patientType;
        $showModal = true;
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

include('../../includes/nurse_header.php');
include('../../includes/nurse_sidebar.php');
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #ffffff;
    }

    .content {
        padding: 40px;
        max-width: 900px;
        margin-left: 250px;
        margin-top: 20px;
    }

    table {
        width: 150%;
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

    form input, form select, form button {
        padding: 8px 12px;
        margin-top: 5px;
        width: 150%;
        box-sizing: border-box;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    form label {
        margin-top: 15px;
        display: block;
        font-weight: 600;
        color: #333;
    }

    button.btn-primary {
        background-color: #6f42c1;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 12px 20px;
        cursor: pointer;
        margin-top: 20px;
        width: auto;
        font-size: 16px;
    }

    button.btn-primary:hover {
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
        padding: 0;
        background-color: #fff;
        max-width: 800px;
        width: 90%;
        box-shadow: 0 0 12px rgba(0,0,0,0.1);
        position: relative;
    }

    .modal-content iframe {
        width: 100%;
        height: 600px;
        border: none;
        border-radius: 12px;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        color: #888;
        cursor: pointer;
        z-index: 1000;
    }

    .close:hover {
        color: #000;
    }
</style>

<div class="content">
    <h2>Add New Patient</h2>
    <form method="POST" action="">
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="dob">Date of Birth:</label>
        <input type="date" name="dob" id="dob" required>

        <label for="sex">Sex:</label>
        <select name="sex" id="sex" required>
            <option value="">Select Sex</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" required>

        <label for="contact">Contact:</label>
        <input type="text" name="contact" id="contact" required>

        <label for="patient_type">Patient Type:</label>
        <select name="patient_type" id="patient_type" required>
            <option value="">Select Type</option>
            <option value="Inpatient">Inpatient</option>
            <option value="Outpatient">Outpatient</option>
        </select>

        <label for="assigned_nurse_id">Assigned Nurse:</label>
        <select name="assigned_nurse_id" id="assigned_nurse_id" required>
            <option value="<?= $loggedInNurseId ?>" selected>Nurse (You)</option>
        </select>

        <button type="submit" class="btn-primary">Add Patient</button>
    </form>

    <hr style="margin: 40px 0; width: 150%;">

    <h3>All Patients</h3>
    <table>
        <thead>
        <tr>
            <th>Patient ID</th>
            <th>Full Name</th>
            <th>DOB</th>
            <th>Sex</th>
            <th>Contact</th>
            <th>Type</th>
            <th>Assigned Nurse</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $query = "SELECT p.PatientID, p.Name, p.DateOfBirth, p.Sex, p.Contact, p.PatientType, n.Name AS NurseName
                  FROM patients p
                  LEFT JOIN nurse n ON p.AssignedNurseID = n.NurseID
                  ORDER BY p.PatientID DESC";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['PatientID']}</td>
                        <td>{$row['Name']}</td>
                        <td>{$row['DateOfBirth']}</td>
                        <td>{$row['Sex']}</td>
                        <td>{$row['Contact']}</td>
                        <td>{$row['PatientType']}</td>
                        <td>" . ($row['NurseName'] ?? 'N/A') . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No patients found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Follow-Up Modal -->
<div id="followUpModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFollowUpModal()">&times;</span>
        <iframe id="followUpIframe"></iframe>
    </div>
</div>

<script>
function closeFollowUpModal() {
    const modal = document.getElementById('followUpModal');
    const iframe = document.getElementById('followUpIframe');
    modal.style.display = 'none';
    iframe.src = '';
}

window.onclick = function(event) {
    const modal = document.getElementById('followUpModal');
    if (event.target === modal) {
        closeFollowUpModal();
    }
};

</script>

<script>
window.addEventListener('message', function(event) {
    if (event.data === 'closeModal') {
        closeFollowUpModal(); // closes the modal
        window.location.href = ''; // reloads the Add Patient page
    }
});
</script>

<?php if ($showModal): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('followUpModal');
        const iframe = document.getElementById('followUpIframe');
        const type = "<?= $modalType ?>";
        const id = <?= $newPatientID ?>;
        iframe.src = type === 'Inpatient' ? 'add_inpatient.php?patient_id=' + id : 'add_outpatient.php?patient_id=' + id;
        modal.style.display = 'flex';
        
    });
</script>
<?php endif; ?>