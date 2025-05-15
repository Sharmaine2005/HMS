<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pharmacist') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/pharmacist_sidebar.php');
include('../../config/db.php');

// Get pharmacy inventory
$medicines = $conn->query("SELECT * FROM pharmacy");

if (isset($_POST['add_medicine'])) {
    $medicine_name = $_POST['medicine_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $stmt = $conn->prepare("INSERT INTO pharmacy (MedicineName, Quantity, Price) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $medicine_name, $quantity, $price);
    $stmt->execute();
    header("Location: pharmacy.php");
    exit();
}

if (isset($_POST['update_medicine'])) {
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];
    $stmt = $conn->prepare("UPDATE pharmacy SET Quantity = ? WHERE PharmacyID = ?");
    $stmt->bind_param("ii", $quantity, $medicine_id);
    $stmt->execute();
    header("Location: pharmacy.php");
    exit();
}

if (isset($_GET['delete'])) {
    $medicine_id = $_GET['delete'];
    $conn->query("DELETE FROM pharmacy WHERE PharmacyID = $medicine_id");
    header("Location: pharmacy.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Pharmacy Management</h2>

    <!-- Add Medicine Form -->
    <form method="POST">
        <label>Medicine Name:</label>
        <input type="text" name="medicine_name" required>
        <label>Quantity:</label>
        <input type="number" name="quantity" required>
        <label>Price:</label>
        <input type="number" step="0.01" name="price" required>
        <button type="submit" name="add_medicine">Add Medicine</button>
    </form>

    <!-- Medicine List -->
    <table border="1">
        <tr>
            <th>Medicine ID</th>
            <th>Medicine Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $medicines->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['PharmacyID'] ?></td>
            <td><?= $row['MedicineName'] ?></td>
            <td><form method="POST">
                <input type="number" name="quantity" value="<?= $row['Quantity'] ?>" required>
                <input type="hidden" name="medicine_id" value="<?= $row['PharmacyID'] ?>">
                <button type="submit" name="update_medicine">Update</button>
            </form></td>
            <td><?= $row['Price'] ?></td>
            <td><a href="?delete=<?= $row['PharmacyID'] ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
