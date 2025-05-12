<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
include('../../config/db.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle update
if (isset($_POST['update_nurse'])) {
    $nurse_id = $_POST['nurse_id'];
    $availability = $_POST['availability'];
    $contact = $_POST['contact'];
    $department_id = $_POST['department_id'];

    $stmt = $conn->prepare("UPDATE nurse SET Availability=?, ContactNumber=?, DepartmentID=? WHERE NurseID=?");
    $stmt->bind_param("ssii", $availability, $contact, $department_id, $nurse_id);
    $stmt->execute();
    header("Location: nurses.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $nurse_id = $_GET['delete'];
    $conn->query("DELETE FROM nurse WHERE NurseID = $nurse_id");
    header("Location: nurses.php");
    exit();
}

// Fetch nurses with department names
$result = $conn->query("SELECT 
        nurse.NurseID, 
        nurse.Name AS NurseName, 
        nurse.Email, 
        nurse.Availability, 
        nurse.ContactNumber, 
        nurse.DepartmentID, 
        department.DepartmentName
    FROM nurse
    LEFT JOIN department ON nurse.DepartmentID = department.DepartmentID
");

// Fetch departments
$departments_result = $conn->query("SELECT DepartmentID, DepartmentName FROM department");
$departments = [];
while ($dept = $departments_result->fetch_assoc()) {
    $departments[] = $dept;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nurse Management</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .content {
            padding: 20px;
        }
        .table-container {
            margin-bottom: 30px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
</head>
<body>
<div class="content">
    <h2>Nurse Management</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>NurseID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Availability</th>
                    <th>Contact Number</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['NurseID'] ?></td>
                            <td><?= htmlspecialchars($row['NurseName']) ?></td>
                            <td><?= htmlspecialchars($row['Email']) ?></td>
                            <td><?= htmlspecialchars($row['Availability']) ?></td>
                            <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                            <td><?= htmlspecialchars($row['DepartmentName']) ?></td>
                            <td>
                                <button class="btn" onclick="openModal(<?= $row['NurseID'] ?>, '<?= htmlspecialchars($row['Availability'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['ContactNumber'], ENT_QUOTES) ?>', <?= $row['DepartmentID'] ?>)">Edit</button>
                                <a href="?delete=<?= $row['NurseID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No nurse records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h3>Edit Nurse Details</h3>
        <form method="post" action="nurses.php">
            <input type="hidden" name="nurse_id" id="modal_nurse_id">

            <div class="form-group">
                <label>Availability</label>
                <input type="text" name="availability" id="modal_availability" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact" id="modal_contact" required>
            </div>
            <div class="form-group">
                <label>Department</label>
                <select name="department_id" id="modal_department_id" required></select>
            </div>
            <button type="submit" name="update_nurse" class="btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
    const departments = <?= json_encode($departments) ?>;

    function openModal(id, availability, contact, departmentId) {
        document.getElementById('modal_nurse_id').value = id;
        document.getElementById('modal_availability').value = availability;
        document.getElementById('modal_contact').value = contact;

        const select = document.getElementById('modal_department_id');
        select.innerHTML = '';
        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept.DepartmentID;
            option.textContent = dept.DepartmentName;
            if (dept.DepartmentID == departmentId) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        document.getElementById('editModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>

</body>
</html>
