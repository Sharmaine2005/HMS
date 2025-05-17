<?php
include('../../config/db.php');
session_start();

$patientID = $_GET['patient_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorID = $_POST['doctor_id'];
    $departmentID = $_POST['department_id'];
    $visitDate = $_POST['visit_date'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO outpatients (PatientID, DoctorID, DepartmentID, VisitDate, Reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $patientID, $doctorID, $departmentID, $visitDate, $reason);

    if ($stmt->execute()) {
        echo "<script>
            alert('Outpatient details added successfully.');
            window.parent.postMessage('closeModal', '*');
        </script>";
        exit;
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Outpatient</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }
        .edit-modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin: 0 auto;
        }
        h3 {
            margin-bottom: 30px;
            font-size: 28px;
            color: #333;
            font-weight: 600;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            resize:none
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
    </style>
</head>
<body>
    <div class="edit-modal-content">
        <h3>Outpatient Details for Patient #<?= $patientID ?></h3>
        <form method="POST">
            <div class="form-group">
                <label for="doctor_id">Doctor ID:</label>
                <input type="number" name="doctor_id" id="doctor_id" required>
            </div>
            <div class="form-group">
                <label for="department_id">Department ID:</label>
                <input type="number" name="department_id" id="department_id" required>
            </div>
            <div class="form-group">
                <label for="visit_date">Visit Date:</label>
                <input type="date" name="visit_date" id="visit_date" required>
            </div>
            <div class="form-group">
                <label for="reason">Reason:</label>
                <textarea name="reason" id="reason" required></textarea>
            </div>
            <button type="submit" class="save-btn">Save Outpatient Info</button>
        </form>
    </div>
</body>
</html>