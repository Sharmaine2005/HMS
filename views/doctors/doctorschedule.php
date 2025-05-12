<?php
session_start();

include('../../includes/doctor_header.php');
include('../../includes/doctor_sidebar.php');
include('../../config/db.php');


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query the doctor schedule table (update the table name if needed)
$sql = "SELECT * FROM doctorschedule";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <head>
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    </head>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
        }

        .content {
            margin-left: 220px; /* sidebar width */
            margin-top: 5px;   /* header height */
            padding: 20px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .schedule-table th, .schedule-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .schedule-table th {
            background-color: #9c335a;
            color: white;
        }

        .schedule-table tr:hover {
            background-color: #f1f1f1;
        }

        .page-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #9c335a;
        }
    </style>
</head>
<body>

<div class="content">
    <h1 class="page-title">My Schedule</h1>

    <?php
    if ($result->num_rows > 0) {
        echo "<table class='schedule-table'>";
        echo "<tr>";
        while ($fieldinfo = $result->fetch_field()) {
            echo "<th>{$fieldinfo->name}</th>";
        }
        echo "</tr>";

        // Output data rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>{$value}</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No doctor schedule found.</p>";
    }

    $conn->close();
    ?>
</div>

</body>
</html>