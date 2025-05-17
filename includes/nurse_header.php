SAMIEyuhhh
less.than_3
Idle

hori — 06/04/2025 4:08 pm
BOOOKINGS
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
11 KB
pa try na pls
SAMIEyuhhh — 06/04/2025 5:00 pm
dashboard
<?php
session_start();
include '../includes/header.php';

// Get the user's name and role from session
$name = $_SESSION['name'] ?? 'User';
Expand
message.txt
5 KB
SAMIEyuhhh — 06/04/2025 5:34 pm
afk
hori — 06/04/2025 5:34 pm
ok
hori — 06/04/2025 6:10 pm
BOOKINGS
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
13 KB
hori — 06/04/2025 6:25 pm
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
11 KB
hori — 06/04/2025 9:45 pm
----------------------------------------------------------------------------------------------------------------------
BOOOKINGS
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
14 KB
SAMIEyuhhh — 06/04/2025 9:46 pm
Attachment file type: document
Vehicle Service Booking System PROJECT REPORT.docx
25.48 KB
SAMIEyuhhh — 06/04/2025 10:27 pm
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
10 KB
SAMIEyuhhh — 06/04/2025 11:04 pm
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
11 KB
latest v of viewbookings
SAMIEyuhhh — 07/04/2025 1:32 am
<?php
session_start();
include("../includes/db.php"); // Move this up!

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $cancel_id = $_POST['cancel_booking_id'];
Expand
message.txt
12 KB
hori — 07/04/2025 10:38 am
Image
SAMIEyuhhh — 07/04/2025 10:39 am
INDEX.PHP

<?php include 'includes/header.php'; ?>

<style>
    .main-content {
        background: url('./images/background.png') no-repeat left center;
        background-size: cover;
        min-height: 100vh;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        padding: 150px 0 0 50px;
    }

    .buttons {
        display: flex;
        justify-content: center;
        gap: 50px;
        position: absolute;
        transform: translate(0px, 400px);
    }
    .btn {
        font-size: 1.8rem;
        font-weight: bold;
        padding: 20px 70px;
        border-radius: 40px;
        text-decoration: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: scale(1.3);
        box-shadow: 10px 8px 16px rgb(16, 146, 207);
    }
    .btn-signup {
        background: #7da6ff;
    }

    .btn-login {
        background: #344c8a;
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 130px 20px 0 20px;
        }

        .buttons {
            / Remove or comment out the following lines /
            / flex-direction: column; /
            / align-items: flex-start; /
            gap: 20px; / Keep the gap smaller for mobile if needed /
        }

        .btn {
            width: auto; / Keep buttons side by side */
            max-width: none;
            text-align: center;
        }
    }
</style>

<div class="main-content">
    <div class="buttons">
        <?php if (isset($_SESSION['role'])): ?>
            <a href="pages/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
        <?php else: ?>
            <a href="/vehicle_booking/pages/register.php" class="btn btn-signup">SIGN UP</a>
            <a href="/vehicle_booking/pages/login.php" class="btn btn-login">LOG IN</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
VIEW_BOOKING
<?php
session_start();
include("../includes/db.php"); // Move this up!

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $cancel_id = $_POST['cancel_booking_id'];
Expand
message.txt
13 KB
REGISTER
<?php
include("../includes/db.php");

$registrationSuccess = false;
$successMessage = "";
Expand
message.txt
9 KB
LOGIN
<?php
include("../includes/db.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
Expand
message.txt
6 KB
EDIT_VEHICLE
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
12 KB
DASHBOARD
<?php
session_start();
include '../includes/header.php';

// Get the user's name and role from session
$name = $_SESSION['name'] ?? 'User';
Expand
message.txt
6 KB
BOOKINGS
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
15 KB
HEADER.PHP
<?php
include(__DIR__ . "/db.php"); // Always relative to includes/ folder
// or whatever your DB connection file is called
?>
<!-- includes/header.php -->
<!DOCTYPE html>
Expand
message.txt
11 KB
DASHBOARD_ADMIN
<?php
include("../includes/db.php");
session_start();

// Check if user is admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
Expand
message.txt
8 KB
Image
SAMIEyuhhh — 07/04/2025 10:49 am
Image
Image
hori — 07/04/2025 11:38 am
Attachment file type: document
Vehicle Service Booking System PROJECT REPORT.docx
25.05 KB
SAMIEyuhhh — 07/04/2025 11:49 am
bookings
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
15 KB
hori — 07/04/2025 11:49 am
<option value="Kawasaki">Kawasaki</option>
                        <option value="Yamaha">Yamaha</option>
                        <option value="Susuki">Susuki</option>
                        <option value="Ducati">Ducati</option>
SAMIEyuhhh — 07/04/2025 11:51 am
bookings
<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: /vehicle_booking/pages/login.php");
Expand
message.txt
15 KB
hori — 07/04/2025 12:27 pm
CREATE TABLE bookings (
    booking_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    vehicle_id INT(11),
    service_id INT(11),
    provider_id INT(11),
    service_name VARCHAR(100),
    booking_date DATETIME,
    status ENUM('pending', 'approved', 'completed', 'cancelled'),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id),
    FOREIGN KEY (service_id) REFERENCES services(service_id),
    FOREIGN KEY (provider_id) REFERENCES service_providers(provider_id)
);
CREATE TABLE users (
    user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100),
    phone_number VARCHAR(15),
    role ENUM('admin', 'user')
);

CREATE TABLE vehicles (
    vehicle_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    make VARCHAR(100),
    model VARCHAR(100),
    year YEAR(4),
    plate_number VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE service_providers (
    provider_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    phone_number VARCHAR(15),
    address TEXT
);
CREATE TABLE services (
    service_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100),
    service_description TEXT,
    price DOUBLE
);
hori — 08/05/2025 4:52 pm
<?php
session_start();
include('../config/db.php');

$error = "";
Expand
message.txt
5 KB
<!-- footer.php -->
<footer style="background-color:rgb(156, 51, 90); color: #ccc; text-align: center; padding: 15px 0; font-size: 14px;">
    2025 - 2025 © Hospital Management System. Developed By Group 5 CHART
</footer>
Image
Image
Image
Image
Image
Image
Image
Image
Image
Image
Image
Image
Image
hori — 08/05/2025 6:35 pm
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #e0f7fa;
    color: #333;
}

.sidebar {
    position: fixed;
    width: 200px;
    height: 100%;
    background: #9c335a;
    padding: 20px;
    box-sizing: border-box;
}

.sidebar h2 {
    color: rgb(255, 255, 255);
    text-align: center;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar ul li {
    margin: 20px 0;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: block;
}

.sidebar ul li a:hover {
    background: #7a0154;
    padding-left: 10px;
    border-radius: 5px;
}

.content {
    margin-left: 220px; /* leave space for sidebar */
    padding: 20px;
}

.button {
    padding: 10px 20px;
    background-color: #0288d1;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.button:hover {
    background-color: #0277bd;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

table, th, td {
    border: 1px solid #b3e5fc;
}

th, td {
    padding: 10px;
    text-align: left;
}

form input, form select {
    padding: 10px;
    margin: 5px 0;
    width: 100%;
    box-sizing: border-box;
}
SAMIEyuhhh — 10/05/2025 3:48 pm
Forwarded
<div class="sidebar">
    <h2>Hospital System</h2>
    <ul>
        <li><a href="/HMS-main/views/admin/dashboard.php">Dashboard</a></li>

        <li>
Expand
message.txt
3 KB
hori — 10/05/2025 3:48 pm
ano yan
tinanggal na yung css na file?
hori — 10/05/2025 9:56 pm
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HMSHomepage</title>
    <style>
        
{
          margin: 0;
          padding: 0;
          box-sizing: border-box;},

        body, html {
            height: 100%;
            font-family: 'Segoe UI', sans-serif;
        }

        .background {
            background: url('../images/banner5.png') no-repeat center center fixed; 
            background-size: full;
            background-position: center;
            height: 100vh;
            width: 100%;
            position: relative;
        }

        .tagline{
            font-family: 'Roboto', sans-serif;
            font-size: 100px;

        }

        .headline{
            font-family: 'Montserrat', sans-serif;
            font-size: 48px;
            font-weight: 700;

        }

        .subtext{
            font-family: 'arial', sans-serif;
            font-size: 16px;
            line-height: 1.5;

        }


        .top-bar {
            position: absolute;
            top: 50px;
            right: 50px;
        }

        .login-button {
            width: 150px;
            height: 65px;
            margin-right: 100px;
            padding: 10px 20px;
            background-color: #ffffffcc;
            color: #333;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #ff6b97;
            color: white;
        }

        .homepage-text {
            position: absolute;
            top: 60%;
            left: 43%;
            transform: translate(-50%, -50%);
            text-align: left;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        .homepage-text h1 {
            font-size: 50px;
            margin-bottom: 10px;
            margin-top: 10px;

        }

        .homepage-text p {
            font-size: 14px;
            margin-right: 290px;

        }
        .homepage-text at {
            font-size: 24px;
            margin-right: 60px;

        }

    </style>
</head>
<body>
    <div class="background">
        <div class="top-bar">
            <a href="login.php">
                <button class="login-button">Login</button>
            </a>
        </div>
        <div class="homepage-text">
            <at class="tagline">Compassion in Care, Excellence in Healing</at>
            <h1 class="headline">Leading the way in medical excellence</h1>
            <p class="subtext">CHART Memorial Hospital has been recognized as one of the Top Healthcare Institutions, known for its advanced integration of cutting-edge technology, streamlined processes, and state-of-the-art medical systems. Our commitment to operational excellence and data-driven decision-making ensures the highest quality of patient care, supported by advanced analytics and intelligent healthcare solutions.</p>
        </div>
    </div>
</body>
</html>
hori — Yesterday at 10:58 pm
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
Expand
emergency.php
5 KB
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
Expand
inpatient.php
9 KB
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
Expand
location.php
3 KB
<?php
session_start();
include('../../includes/nurse_header.php');
include('../../includes/nurse_sidebar.php');
// Ensure the nurse is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
Expand
my_patients.php
10 KB
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
Expand
outpatient.php
9 KB
<?php
session_start();
include('../../includes/nurse_header.php');
include('../../includes/nurse_sidebar.php');

// Ensure the nurse is logged in
Expand
patient.php
10 KB
<?php
include('../../config/db.php');
session_start();

if (!isset($_SESSION['role_id'])) {
    echo "Access denied.";
Expand
add_appointment.php
6 KB
<?php
include('../../config/db.php');
session_start();

if (!isset($_SESSION['role_id'])) {
    echo "Access denied.";
Expand
add_patient.php
8 KB
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
Expand
dashboard.php
1 KB
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
Expand
department.php
3 KB
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
Expand
doctorschedule.php
8 KB
<?php
// Include the database connection
include('../../config/db.php');

$nurseName = "Unknown Nurse";
Expand
nurse_header.php
4 KB
<div class="sidebar">
    <h2>Hospital System</h2>
    <ul>
        <li><a href="/HMS-main/views/nurse/dashboard.php">Dashboard</a></li>

        <li>
Expand
nurse_sidebar.php
4 KB
﻿
<?php
// Include the database connection
include('../../config/db.php');

$nurseName = "Unknown Nurse";

if (isset($_SESSION['role']) && $_SESSION['role'] === 'nurse' && isset($_SESSION['role_id'])) {
    $nurseID = $_SESSION['role_id'];

    // Fetch nurse name from DB
    $stmt = $conn->prepare("SELECT Name FROM nurse WHERE NurseID = ?");
    $stmt->bind_param("i", $nurseID);
    $stmt->execute();
    $stmt->bind_result($fetchedName);
    if ($stmt->fetch()) {
        $nurseName = $fetchedName;
    }
    $stmt->close();
} else {
    $nurseID = "Unknown";
}
?>

<style>
  /* Your CSS remains the same */
  .header {
    position: fixed;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #eb6d9b;
    padding: 10px 20px;
    color: white;
    width: 98%;
    height: 60px;
    z-index: 10;
    top: 0;
  }

  .left-section,
  .right-section {
      display: flex;
      align-items: center;
      gap: 15px;
  }

  .logo {
      height: 40px;
  }

  .menu-icon {
      font-size: 20px;
      cursor: pointer;
  }

  .dropbtn {
      background: none;
      border: none;
      color: white;
      font-size: 16px;
      cursor: pointer;
  }

  .dropdown {
      position: relative;
      display: inline-block;
  }

  .dropdown-content {
      display: none;
      position: absolute;
      background-color: #3e4a56;
      min-width: 150px;
      z-index: 1;
      top: 100%;
      left: 0;
  }

  .dropdown-content a {
      color: white;
      padding: 10px;
      display: block;
      text-decoration: none;
  }

  .dropdown-content a:hover {
      background-color: #5a6570;
  }

  .dropdown:hover .dropdown-content {
      display: block;
  }

  .search-section {
      display: flex;
      align-items: center;
      background: #fcc0ef;
      border-radius: 20px;
      padding: 5px 10px;
  }

  .search-section input {
      background: transparent;
      border: none;
      outline: none;
      color: white;
      padding: 5px;
      width: 200px;
  }

  .search-icon {
      margin-left: 5px;
      color: #cc8383;
  }

  .avatar {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      border: 2px solid #4caf50;
  }

  .user-dropdown {
      cursor: pointer;
      font-size: 14px;
  }
</style>

<div class="header">
    <div class="left-section">
        <img src="../../images/hosplogo.png" alt="Logo" class="logo">
        <i class="fas fa-bars menu-icon"></i>
    </div>

    <div class="search-section">
        <input type="text" placeholder="Search...">
        <i class="fas fa-search search-icon"></i>
    </div>

    <div class="right-section">
        <img src="../../assets/user.png" alt="Avatar" class="avatar">
        <div class="user-dropdown">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'nurse'): ?>
                <span> <?php echo htmlspecialchars($nurseName); ?> <i class="fas fa-chevron-down"></i></span>
            <?php endif; ?>
        </div>
    </div>
</div>