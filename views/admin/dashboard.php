<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
include('../../config/db.php');

$admin_name = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
<h2>Welcome, <?php echo htmlspecialchars($admin_name); ?>!</h2>
    <p>This is your admin dashboard. Use the sidebar to navigate.</p>
</div>

</body>
</html>