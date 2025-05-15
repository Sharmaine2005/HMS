<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../config/db.php');

// Handle update
if (isset($_POST['update_cashier'])) {
    $cashier_id = $_POST['cashier_id'];
    $name = $_POST['name'];

    $stmt = $conn->prepare("UPDATE cashier SET Name=? WHERE CashierID=?");
    $stmt->bind_param("si", $name, $cashier_id);
    $stmt->execute();
    header("Location: cashier.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM cashier WHERE CashierID = $id");
    header("Location: cashier.php");
    exit();
}

// Fetch cashiers
$result = $conn->query("SELECT * FROM cashier");
include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Management</title>
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
    <h2>Cashier Management</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cashier Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['CashierID'] ?></td>
                            <td><?= htmlspecialchars($row['Name']) ?></td>
                            <td>
                                <button class="btn" onclick="openModal(
                                    <?= $row['CashierID'] ?>,
                                    '<?= htmlspecialchars($row['Name'], ENT_QUOTES) ?>'
                                )">Edit</button>
                                <a href="?delete=<?= $row['CashierID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">No cashier records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h3>Edit Cashier Details</h3>
        <form method="post" action="cashier.php">
            <input type="hidden" name="cashier_id" id="modal_cashier_id">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="modal_name" required>
            </div>
            <button type="submit" name="update_cashier" class="sbtn">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openModal(id, name) {
    document.getElementById('modal_cashier_id').value = id;
    document.getElementById('modal_name').value = name;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

</body>
</html>
