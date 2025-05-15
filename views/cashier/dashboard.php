<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../config/db.php');
include('../../includes/cashier_header.php');
include('../../includes/cashier_sidebar.php');

$cashier_name = $_SESSION['username'];
?>


<!DOCTYPE html>
<html>
<head>
    <title>Cashier Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
<h2>Welcome, <?php echo htmlspecialchars($cashier_name); ?>!</h2>
    <p>This is your cashier dashboard. Use the sidebar to navigate.</p>
</div>
    
</body>
</html>