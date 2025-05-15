<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../config/db.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle update (edit doctor details)
if (isset($_POST['update_doctor'])) {
    $doctor_id = $_POST['doctor_id'];
    $availability = $_POST['availability'];
    $contact = $_POST['contact'];
    $doctor_type = $_POST['doctor_type'];
    $department_id = $_POST['department_id'];
    $doctor_fee = $_POST['doctor_fee'];

    $stmt = $conn->prepare("UPDATE doctor SET Availability=?, ContactNumber=?, DoctorType=?, DepartmentID=?, DoctorFee=? WHERE DoctorID=?");
    $stmt->bind_param("sssisi", $availability, $contact, $doctor_type, $department_id, $doctor_fee, $doctor_id);
    $stmt->execute();
    header("Location: doctors.php");
    exit();
}

// Handle delete doctor and user
if (isset($_GET['delete'])) {
    $doctor_id = $_GET['delete'];
    $result = $conn->query("SELECT UserID FROM doctor WHERE DoctorID = $doctor_id");
    if ($row = $result->fetch_assoc()) {
        $user_id = $row['UserID'];
        $conn->query("DELETE FROM doctor WHERE DoctorID = $doctor_id");
        $conn->query("DELETE FROM users WHERE id = $user_id");
    }
    header("Location: doctors.php");
    exit();
}

// Fetch doctors with user info
$result = $conn->query("SELECT d.DoctorID, u.username AS DoctorName, u.email AS Email, d.Availability, d.ContactNumber, d.DoctorType, d.DepartmentID, d.DoctorFee 
                        FROM doctor d 
                        JOIN users u ON d.UserID = u.UserID");
include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Management</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .edit-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .edit-modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 800px;
            max-width: 90%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .modal-close {
            position: absolute;
            right: 24.1%;
            top: 3%;
            padding: 10px;
            font-size: 30px;
            color: #fff;
            cursor: pointer;
        }
        .modal-close:hover {
            color: #f44336;
        }
        h3 {
            margin-bottom: 30px;
            font-size: 28px;
            color: #333;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        .save-btn {
            width: 100%;
            padding: 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .save-btn:hover {
            background-color: #45a049;
        }
        .content {
            padding: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        function showEditForm(id) {
            document.getElementById('edit-modal-' + id).style.display = 'flex';
        }
        function closeModal(id) {
            document.getElementById('edit-modal-' + id).style.display = 'none';
        }
    </script>
</head>
<body>
<div class="content">
    <h2>Doctor Management</h2>
    <table>
        <tr>
            <th>DoctorID</th>
            <th>DoctorName</th>
            <th>Email</th>
            <th>Availability</th>
            <th>ContactNumber</th>
            <th>DoctorType</th>
            <th>Department</th>
            <th>DoctorFee</th>
            <th>Action</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['DoctorID'] ?></td>
                    <td><?= htmlspecialchars($row['DoctorName']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['Availability']) ?></td>
                    <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                    <td><?= htmlspecialchars($row['DoctorType']) ?></td>
                    <td><?= htmlspecialchars($row['DepartmentID']) ?></td>
                    <td><?= htmlspecialchars($row['DoctorFee']) ?></td>
                    <td>
                        <button onclick="showEditForm(<?= $row['DoctorID'] ?>)">Edit</button> |
                        <a href="?delete=<?= $row['DoctorID'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <tr id="edit-modal-<?= $row['DoctorID'] ?>" class="edit-modal">
                    <td colspan="9">
                        <div class="edit-modal-content">
                            <span class="modal-close" onclick="closeModal(<?= $row['DoctorID'] ?>)">×</span>
                            <h3>Edit Doctor Details</h3>
                            <form method="post" action="doctors.php">
                                <input type="hidden" name="doctor_id" value="<?= $row['DoctorID'] ?>">

                                <div class="form-group">
                                    <label>Availability</label>
                                    <input type="text" name="availability" value="<?= htmlspecialchars($row['Availability']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact" value="<?= htmlspecialchars($row['ContactNumber']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Doctor Type</label>
                                    <input type="text" name="doctor_type" value="<?= htmlspecialchars($row['DoctorType']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Department</label>
                                    <select name="department_id" required>
                                        <?php
                                        $dept_result = $conn->query("SELECT DepartmentID, DepartmentName FROM department");
                                        while ($dept = $dept_result->fetch_assoc()):
                                        ?>
                                            <option value="<?= $dept['DepartmentID'] ?>" <?= $row['DepartmentID'] == $dept['DepartmentID'] ? 'selected' : '' ?>>
                                                <?= $dept['DepartmentName'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Doctor Fee</label>
                                    <input type="number" name="doctor_fee" step="0.01" value="<?= htmlspecialchars($row['DoctorFee']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($row['Email']) ?>" disabled>
                                </div>
                                <button type="submit" name="update_doctor" class="save-btn">Save Changes</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9">No doctor records found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
