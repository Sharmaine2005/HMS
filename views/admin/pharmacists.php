<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}


include('../../config/db.php');

// Handle update
if (isset($_POST['update_pharmacist'])) {
    $pharmacist_id = $_POST['pharmacist_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    $stmt = $conn->prepare("UPDATE pharmacist SET Name=?, Email=?, ContactNumber=? WHERE PharmacistID=?");
    $stmt->bind_param("sssi", $name, $email, $contact, $pharmacist_id);
    $stmt->execute();
    header("Location: pharmacists.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM pharmacist WHERE PharmacistID = $id");
    header("Location: pharmacists.php");
    exit();
}

$result = $conn->query("SELECT * FROM pharmacist");
include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacist Management</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .content {
            padding: 20px;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        .sbtn {
            padding: 5px 10px;
            background-color: #007bff;
            margin-top: 20px;
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
        
        .form-group { /*Edit pharm*/
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        }
        .form-group label {
        margin-bottom: 5px;
        font-weight: bold;
        }
        .form-group input {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        }

        /* Modal Styles */
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
            width: 400px;
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
    <h2>Pharmacist Management</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pharmacist Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['PharmacistID'] ?></td>
                        <td><?= htmlspecialchars($row['Name']) ?></td>
                        <td><?= htmlspecialchars($row['Email']) ?></td>
                        <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                        <td>
                            <button class="btn" onclick="openModal(
                                <?= $row['PharmacistID'] ?>,
                                '<?= htmlspecialchars($row['Name'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['Email'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['ContactNumber'], ENT_QUOTES) ?>'
                            )">Edit</button>
                            <a href="?delete=<?= $row['PharmacistID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No pharmacist records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h3>Edit Pharmacist Details</h3>
        <form method="post" action="pharmacists.php">
            <input type="hidden" name="pharmacist_id" id="modal_pharmacist_id">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="modal_name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="modal_email" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact" id="modal_contact" required>
            </div>
            <button type="submit" name="update_pharmacist" class="sbtn">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openModal(id, name, email, contact) {
    document.getElementById('modal_pharmacist_id').value = id;
    document.getElementById('modal_name').value = name;
    document.getElementById('modal_email').value = email;
    document.getElementById('modal_contact').value = contact;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

</body>
</html>
