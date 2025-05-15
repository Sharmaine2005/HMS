<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../config/db.php');

// Billing Summary Report
$billing = $conn->query("
    SELECT p.PatientID, p.Name AS PatientName, p.DateOfBirth, p.Contact, p.Sex, p.Address, 
           b.DoctorFee, b.MedicineCost, b.TotalAmount
    FROM patientbilling b 
    JOIN patients p ON b.PatientID = p.PatientID
");

// Doctor List
$reports = $conn->query("
    SELECT doctor.*, dept.DepartmentName
    FROM doctor
    LEFT JOIN department dept ON doctor.DepartmentID = dept.DepartmentID
");

// Nurses List
$nurses = $conn->query("SELECT * FROM nurse");
include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <style>
        .content {
            padding: 20px;
        }
        h2, h3 {
            margin-top: 30px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        table th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            margin-bottom: 15px;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
        }
        .button:hover {
            background-color: #0056b3;
        }
        form {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="content">
    <h2>Reports</h2>

    <!-- Billing Summary -->
    <h3>Billing Summary</h3>

    <form method="post" action="export_billing_pdf.php">
        <button type="submit" class="button">Export Billing to PDF</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Date of Birth</th>
                <th>Contact</th>
                <th>Sex</th>
                <th>Address</th>
                <th>Doctor Fee</th>
                <th>Medicine Cost</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($billing->num_rows > 0) {
                while ($row = $billing->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['PatientID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['PatientName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['DateOfBirth']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Contact']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Sex']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['DoctorFee']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['MedicineCost']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['TotalAmount']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No billing data available.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Doctors List -->
    <h3>Doctors List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Specialty</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($reports->num_rows > 0) {
                while ($row = $reports->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['DoctorID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['DoctorName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['DoctorType']) . "</td>";
                    echo "<td>" . ($row['DepartmentName'] ?? 'Not Assigned') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No doctors available.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Nurses List -->
    <h3>Nurses List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nurse Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($nurses->num_rows > 0) {
                while ($row = $nurses->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['NurseID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No nurses available.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</div>

</body>
</html>
