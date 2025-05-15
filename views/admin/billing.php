<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../config/db.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctors_result = $conn->query("SELECT DoctorID, DoctorName, DoctorFee FROM doctor");
$patients_result = $conn->query("SELECT PatientID, Name FROM patients");

$result = $conn->query("SELECT MAX(CAST(Receipt AS UNSIGNED)) AS last_receipt FROM patientbilling");
$row = $result->fetch_assoc();
$last_receipt = $row['last_receipt'] ?? 0;
$new_receipt_number = str_pad($last_receipt + 1, 6, '0', STR_PAD_LEFT);

$bills_result = $conn->query("SELECT b.*, p.Name AS PatientName, d.DoctorName
FROM patientbilling b
JOIN patients p ON b.PatientID = p.PatientID
JOIN doctor d ON b.DoctorID = d.DoctorID;");

$user_role = $_SESSION['role'] ?? '';
$can_edit = $user_role == 'doctor';
include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing Management</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .content { padding: 20px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f4f4f4;
        }
        tr:hover { background-color: #f9f9f9; }
        .btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover { background-color: #0056b3; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; }
        form {
            margin-bottom: 20px;
        }
        form label {
            display: block;
            margin: 10px 0 4px;
        }
        form input, form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
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

    <?php if ($can_edit): ?>
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
            <input type="number" name="doctor_fee" id="doctorFee" step="0.01" readonly required>

            <label>Medicine Cost:</label>
            <input type="number" name="medicine_cost" placeholder="Enter Medicine Cost" step="0.01" required>

            <label>Payment Date:</label>
            <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>

            <label>Receipt Number:</label>
            <input type="text" name="receipt" id="receipt" value="<?php echo $new_receipt_number; ?>" readonly>

            <button type="submit" name="add_bill" class="btn">Add Bill</button>
        </form>
    <?php else: ?>
        <p>You do not have permission to add bills.</p>
    <?php endif; ?>

    <table>
        <thead>
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
        </thead>
        <tbody>
        <?php while ($row = $bills_result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['BillingID'] ?></td>
                <td><?= htmlspecialchars($row['PatientName']) ?></td>
                <td><?= htmlspecialchars($row['DoctorName']) ?></td>
                <td><?= htmlspecialchars($row['DoctorFee']) ?></td>
                <td><?= htmlspecialchars($row['MedicineCost']) ?></td>
                <td><?= htmlspecialchars($row['TotalAmount']) ?></td>
                <td><?= htmlspecialchars($row['PaymentDate']) ?></td>
                <td><?= htmlspecialchars($row['Receipt']) ?></td>
                <td>
                    <?php if ($can_edit): ?>
                        <button class="btn" onclick="openModal(
                            <?= $row['BillingID'] ?>,
                            <?= $row['PatientID'] ?>,
                            <?= $row['DoctorID'] ?>,
                            <?= $row['DoctorFee'] ?>,
                            <?= $row['MedicineCost'] ?>,
                            '<?= $row['PaymentDate'] ?>',
                            '<?= $row['Receipt'] ?>'
                        )">Edit</button>
                        <a href="?delete=<?= $row['BillingID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php else: ?>
                        <span>No action allowed</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h3>Edit Billing</h3>
        <form method="post" action="billing.php">
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
            <input type="number" name="doctor_fee" id="modal_doctor_fee" step="0.01" readonly required>

            <label>Medicine Cost:</label>
            <input type="number" name="medicine_cost" id="modal_medicine_cost" step="0.01" required>

            <label>Payment Date:</label>
            <input type="date" name="payment_date" id="modal_payment_date" required>

            <label>Receipt:</label>
            <input type="text" name="receipt" id="modal_receipt" required>

            <button type="submit" name="update_bill" class="btn">Save</button>
        </form>
    </div>
</div>

<script>
function updateDoctorFee() {
    const select = document.getElementById('doctorSelect');
    const fee = select.options[select.selectedIndex].getAttribute('data-fee');
    document.getElementById('doctorFee').value = fee || '';
}

function updateModalDoctorFee() {
    const select = document.getElementById('modal_doctor_id');
    const fee = select.options[select.selectedIndex].getAttribute('data-fee');
    document.getElementById('modal_doctor_fee').value = fee || '';
}

function openModal(billing_id, patient_id, doctor_id, doctor_fee, medicine_cost, payment_date, receipt) {
    document.getElementById('modal_billing_id').value = billing_id;
    document.getElementById('modal_patient_id').value = patient_id;
    document.getElementById('modal_doctor_id').value = doctor_id;
    document.getElementById('modal_doctor_fee').value = doctor_fee;
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
