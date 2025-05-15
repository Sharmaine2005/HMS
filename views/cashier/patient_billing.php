<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../config/db.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctors
$doctors_result = $conn->query("SELECT DoctorID, DoctorName, DoctorFee FROM doctor");

// Fetch patients
$patients_result = $conn->query("SELECT PatientID, Name FROM patients");

// Generate receipt number
$result = $conn->query("SELECT MAX(CAST(Receipt AS UNSIGNED)) AS last_receipt FROM patientbilling");
$row = $result->fetch_assoc();
$last_receipt = $row['last_receipt'] ?? 0;
$new_receipt_number = str_pad($last_receipt + 1, 6, '0', STR_PAD_LEFT);

// Add bill
if (isset($_POST['add_bill'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $doctor_fee = (float) $_POST['doctor_fee'];
    $medicine_cost = (float) $_POST['medicine_cost'];
    $payment_date = $_POST['payment_date'];
    $receipt = $_POST['receipt'];
    $total = $doctor_fee + $medicine_cost;

    $stmt = $conn->prepare("INSERT INTO patientbilling (PatientID, DoctorID, DoctorFee, MedicineCost, TotalAmount, PaymentDate, Receipt) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidddds", $patient_id, $doctor_id, $doctor_fee, $medicine_cost, $total, $payment_date, $receipt);
    $stmt->execute();
    header("Location: patient_billing.php");
    exit();
}

// Update bill
if (isset($_POST['update_bill'])) {
    $billing_id = $_POST['billing_id'];
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $doctor_fee = $_POST['doctor_fee'];
    $medicine_cost = $_POST['medicine_cost'];
    $payment_date = $_POST['payment_date'];
    $receipt = $_POST['receipt'];
    $total = $doctor_fee + $medicine_cost;

    $stmt = $conn->prepare("UPDATE patientbilling SET PatientID=?, DoctorID=?, DoctorFee=?, MedicineCost=?, TotalAmount=?, PaymentDate=?, Receipt=? WHERE BillingID=?");
    $stmt->bind_param("iiddsssi", $patient_id, $doctor_id, $doctor_fee, $medicine_cost, $total, $payment_date, $receipt, $billing_id);
    
    if (!$stmt->execute()) {
        die("Update failed: " . $stmt->error);
    }

    header("Location: patient_billing.php");  // <-- Redirect here after update
    exit();
}

// Delete bill
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM patientbilling WHERE BillingID=$id");
    header("Location: patient_billing.php");
    exit();
}

// Fetch bills
$bills_result = $conn->query("SELECT b.*, p.PatientID, p.Name AS PatientName, d.DoctorID, d.DoctorName
FROM patientbilling b
JOIN patients p ON b.PatientID = p.PatientID
JOIN doctor d ON b.DoctorID = d.DoctorID;");
include('../../includes/cashier_header.php');
include('../../includes/cashier_sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing Management</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 10px; right: 10px;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
</head>
<body>

<div class="content">
    <h2>Billing Management</h2>

    <form method="post" action="">
        <label>Patient:</label>
        <select name="patient_id" required>
            <option value="">Select Patient</option>
            <?php
            $patients_result->data_seek(0);
            while ($p = $patients_result->fetch_assoc()) {
                echo "<option value='{$p['PatientID']}'>".htmlspecialchars($p['Name'])."</option>";
            }
            ?>
        </select>

        <label>Doctor:</label>
        <select name="doctor_id" id="doctorSelect" onchange="updateDoctorFee()" required>
            <option value="">Select Doctor</option>
            <?php
            $doctors_result->data_seek(0);
            while ($d = $doctors_result->fetch_assoc()) {
                echo "<option value='{$d['DoctorID']}' data-fee='{$d['DoctorFee']}'>".htmlspecialchars($d['DoctorName'])."</option>";
            }
            ?>
        </select>

        <label>Doctor Fee:</label>
        <input type="text" id="doctorFeeDisplay" readonly>
        <input type="hidden" name="doctor_fee" id="doctorFee" required>

        <input type="number" name="medicine_cost" placeholder="Enter Medicine Cost ₱" step="0.01" required>
        <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>

        <label>Receipt Number:</label>
        <input type="text" name="receipt" value="<?php echo $new_receipt_number; ?>" readonly>

        <button type="submit" name="add_bill" style="padding: 15px 26px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Add Bill
        </button>
    </form>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Doctor Fee</th>
            <th>Medicine Cost</th>
            <th>Total Amount</th>
            <th>Payment Date</th>
            <th>Receipt</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $bills_result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['BillingID'] ?></td>
                <td><?= htmlspecialchars($row['PatientName']) ?></td>
                <td><?= htmlspecialchars($row['DoctorName']) ?></td>
                <td>₱<?= number_format($row['DoctorFee'], 2) ?></td>
                <td>₱<?= number_format($row['MedicineCost'], 2) ?></td>
                <td>₱<?= number_format($row['TotalAmount'], 2) ?></td>
                <td><?= htmlspecialchars($row['PaymentDate']) ?></td>
                <td><?= htmlspecialchars($row['Receipt']) ?></td>
                <td>
                    <button onclick="openModal(
                        <?= $row['BillingID'] ?>,
                        <?= $row['PatientID'] ?>,
                        <?= $row['DoctorID'] ?>,
                        <?= $row['DoctorFee'] ?>,
                        <?= $row['MedicineCost'] ?>,
                        '<?= $row['PaymentDate'] ?>',
                        '<?= $row['Receipt'] ?>'
                    )">Edit</button>
                    |
                    <a href="?delete=<?= $row['BillingID'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h3>Edit Billing</h3>
        <form method="post" action="patient_billing.php">
            <input type="hidden" name="billing_id" id="modal_billing_id">
            <label>Patient:</label>
            <select name="patient_id" id="modal_patient_id" required>
                <option value="">Select Patient</option>
                <?php
                $patients_result->data_seek(0);
                while ($p = $patients_result->fetch_assoc()) {
                    echo "<option value='{$p['PatientID']}'>".htmlspecialchars($p['Name'])."</option>";
                }
                ?>
            </select>
            <label>Doctor:</label>
            <select name="doctor_id" id="modal_doctor_id" onchange="updateModalDoctorFee()" required>
                <option value="">Select Doctor</option>
                <?php
                $doctors_result->data_seek(0);
                while ($d = $doctors_result->fetch_assoc()) {
                    echo "<option value='{$d['DoctorID']}' data-fee='{$d['DoctorFee']}'>".htmlspecialchars($d['DoctorName'])."</option>";
                }
                ?>
            </select>
            <label>Doctor Fee:</label>
            <input type="text" id="modal_doctor_fee_display" readonly>
            <input type="hidden" name="doctor_fee" id="modal_doctor_fee" required>
            <input type="number" name="medicine_cost" id="modal_medicine_cost" step="0.01" required>
            <input type="date" name="payment_date" id="modal_payment_date">
            <input type="text" name="receipt" id="modal_receipt" required>
            <button type="submit" name="update_bill">Save</button>
        </form>
    </div>
</div>


<script>
function updateDoctorFee() {
    const select = document.getElementById('doctorSelect');
    const fee = select.options[select.selectedIndex].getAttribute('data-fee');
    document.getElementById('doctorFee').value = fee || '';
    document.getElementById('doctorFeeDisplay').value = fee ? `₱${parseFloat(fee).toFixed(2)}` : '';
}

function updateModalDoctorFee() {
    const select = document.getElementById('modal_doctor_id');
    const fee = select.options[select.selectedIndex].getAttribute('data-fee');
    document.getElementById('modal_doctor_fee').value = fee || '';
    document.getElementById('modal_doctor_fee_display').value = fee ? `₱${parseFloat(fee).toFixed(2)}` : '';
}

function openModal(billing_id, patient_id, doctor_id, doctor_fee, medicine_cost, payment_date, receipt) {
    document.getElementById('modal_billing_id').value = billing_id;
    document.getElementById('modal_patient_id').value = patient_id;
    document.getElementById('modal_doctor_id').value = doctor_id;
    document.getElementById('modal_doctor_fee').value = doctor_fee;
    document.getElementById('modal_doctor_fee_display').value = `₱${parseFloat(doctor_fee).toFixed(2)}`;
    document.getElementById('modal_medicine_cost').value = medicine_cost;
    document.getElementById('modal_payment_date').value = payment_date;
    document.getElementById('modal_receipt').value = receipt;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

</body>
</html>