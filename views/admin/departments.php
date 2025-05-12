<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
include('../../config/db.php');

// Add department
if (isset($_POST['add_department'])) {
    $dept_name = $_POST['department_name'];
    $dept_room = $_POST['department_room'];
    $stmt = $conn->prepare("INSERT INTO department (DepartmentName, DepartmentRoom) VALUES (?, ?)");
    $stmt->bind_param("ss", $dept_name, $dept_room);
    $stmt->execute();
    header("Location: departments.php");
    exit();
}

// Delete department
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM department WHERE DepartmentID = $id");
    header("Location: departments.php");
    exit();
}

include('../../includes/admin_sidebar.php'); // Move after logic
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Department Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .content {
            padding: 20px;
        }

        .form-input {
            margin: 5px 0;
            padding: 8px;
            width: 100%;
        }

        .form-container {
            max-width: 400px;
            margin: 20px auto;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .form-container input {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container button {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="content">
    <h2>Department Management</h2>

    <!-- Department Form -->
    <div class="form-container">
        <form method="post" action="">
            <input type="text" name="department_name" placeholder="Enter Department Name" required>
            <input type="text" name="department_room" placeholder="Enter Department Room" required>
            <button type="submit" name="add_department">Add Department</button>
        </form>
    </div>

    <!-- Department Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Department Name</th>
            <th>Department Room</th>
            <th>Action</th>
        </tr>

        <?php
        // Fetch all departments
        $result = $conn->query("SELECT * FROM department");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['DepartmentID']."</td>
                    <td>".$row['DepartmentName']."</td>
                    <td>".$row['DepartmentRoom']."</td>
                    <td><a href='?delete=".$row['DepartmentID']."' onclick=\"return confirm('Are you sure?')\">Delete</a></td>
                </tr>";
        }
        ?>
    </table>

</div>

</body>
</html>
