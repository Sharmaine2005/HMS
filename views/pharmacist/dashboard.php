<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pharmacist') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/pharmacist_sidebar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacist Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Welcome, Pharmacist!</h2>
    <p>This is your Pharmacist dashboard. Use the sidebar to navigate.</p>
</div>
    
</body>
</html>
