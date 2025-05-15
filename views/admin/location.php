<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../config/db.php');

// Handle Add
if (isset($_POST['add_location'])) {
    $roomType = $_POST['RoomType'];
    $roomCapacity = $_POST['RoomCapacity'];
    $availability = $_POST['Availability'];
    $building = $_POST['Building'];
    $floor = $_POST['Floor'];
    $roomNumber = $_POST['RoomNumber'];
    $locationName = "Building $building, Floor $floor, Room $roomNumber";

    $stmt = $conn->prepare("INSERT INTO locations (RoomType, RoomCapacity, Availability, Building, Floor, RoomNumber, LocationName) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssis", $roomType, $roomCapacity, $availability, $building, $floor, $roomNumber, $locationName);

    $stmt->execute();
    header("Location: location.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM locations WHERE LocationID = $id");
    header("Location: location.php");
    exit();
}

// Handle Edit
if (isset($_POST['edit_location'])) {
    $locationID = $_POST['LocationID'];
    $roomType = $_POST['RoomType'];
    $roomCapacity = $_POST['RoomCapacity'];
    $availability = $_POST['Availability'];
    $building = $_POST['Building'];
    $floor = $_POST['Floor'];
    $roomNumber = $_POST['RoomNumber'];
    $locationName = "Building $building, Floor $floor, Room $roomNumber";

    $stmt = $conn->prepare("UPDATE locations SET RoomType=?, RoomCapacity=?, Availability=?, Building=?, Floor=?, RoomNumber=?, LocationName=? WHERE LocationID=?");
    $stmt->bind_param("sisssisi", $roomType, $roomCapacity, $availability, $building, $floor, $roomNumber, $locationName, $locationID);
    $stmt->execute();
    header("Location: location.php");
    exit();
}

include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Location Management</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .content {
            padding: 20px;
        }

        .form-container {
            max-width: 500px;
            margin: 20px auto;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .form-container input,
        .form-container select {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
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

        .action-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            margin-right: 5px;
        }

        .action-btn:hover {
            background-color: #45a049;
        }

        .delete-link {
            color: red;
            text-decoration: none;
        }

        .delete-link:hover {
            text-decoration: underline;
        }

        /* Modal styles */
        .edit-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .edit-modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 25px;
            cursor: pointer;
        }

        .modal-close:hover {
            color: red;
        }
    </style>
</head>
<body>

<div class="content">
    <h2>Location Management</h2>

    <!-- Add Location Form -->
    <div class="form-container">
        <form method="post" action="">
            <select name="RoomType" required>
                <option value="" disabled selected>Select Room Type</option>
                <option value="Ward">Ward</option>
                <option value="Private">Private</option>
                <option value="Semi-Private">Semi-Private</option>
            </select>

            <input type="number" name="RoomCapacity" placeholder="Enter Room Capacity" required>
            
            <select name="Availability" required>
                <option value="" disabled selected>Select Availability</option>
                <option value="Occupied">Occupied</option>
                <option value="Unoccupied">Unoccupied</option>
            </select>

            <input type="text" name="Building" placeholder="Enter Building Name" required>
            <input type="number" name="Floor" placeholder="Enter Floor Number" required>
            <input type="number" name="RoomNumber" placeholder="Enter Room Number" required>

            <button type="submit" name="add_location">Add Location</button>
        </form>
    </div>

    <!-- Location Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Room Type</th>
            <th>Capacity</th>
            <th>Availability</th>
            <th>Building</th>
            <th>Floor</th>
            <th>Room Number</th>
            <th>Action</th>
        </tr>

        <?php
        $result = $conn->query("SELECT * FROM locations");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['LocationID']}</td>
                    <td>{$row['RoomType']}</td>
                    <td>{$row['RoomCapacity']}</td>
                    <td>{$row['Availability']}</td>
                    <td>{$row['Building']}</td>
                    <td>{$row['Floor']}</td>
                    <td>{$row['RoomNumber']}</td>
                    <td>
                        <button class='action-btn' onclick=\"document.getElementById('edit-modal-{$row['LocationID']}').style.display = 'flex';\">Edit</button> |
                        <a href='?delete={$row['LocationID']}' class='delete-link' onclick=\"return confirm('Are you sure?')\">Delete</a>
                    </td>
                </tr>";
        }
        ?>
    </table>
</div>

<!-- Edit Modals -->
<?php
$result = $conn->query("SELECT * FROM locations");
while ($row = $result->fetch_assoc()) {
    ?>
    <div id="edit-modal-<?= $row['LocationID'] ?>" class="edit-modal">
        <div class="edit-modal-content">
            <span class="modal-close" onclick="document.getElementById('edit-modal-<?= $row['LocationID'] ?>').style.display = 'none';">×</span>
            <h3>Edit Location</h3>
            <form method="post" action="">
                <input type="hidden" name="LocationID" value="<?= $row['LocationID'] ?>">

                <select name="RoomType" required>
                    <option value="Ward" <?= $row['RoomType'] == 'Ward' ? 'selected' : '' ?>>Ward</option>
                    <option value="Private" <?= $row['RoomType'] == 'Private' ? 'selected' : '' ?>>Private</option>
                    <option value="Semi-Private" <?= $row['RoomType'] == 'Semi-Private' ? 'selected' : '' ?>>Semi-Private</option>
                </select>

                <input type="number" name="RoomCapacity" value="<?= $row['RoomCapacity'] ?>" placeholder="Enter Room Capacity" required>
                
                <select name="Availability" required>
                    <option value="Occupied" <?= $row['Availability'] == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                    <option value="Unoccupied" <?= $row['Availability'] == 'Unoccupied' ? 'selected' : '' ?>>Unoccupied</option>
                </select>

                <input type="text" name="Building" value="<?= $row['Building'] ?>" placeholder="Enter Building Name" required>
                <input type="number" name="Floor" value="<?= $row['Floor'] ?>" placeholder="Enter Floor Number" required>
                <input type="number" name="RoomNumber" value="<?= $row['RoomNumber'] ?>" placeholder="Enter Room Number" required>

                <button type="submit" name="edit_location">Save Changes</button>
            </form>
        </div>
    </div>
<?php } ?>

</body>
</html>
