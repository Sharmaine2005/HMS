<?php
ob_start();  // Start output buffering

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
include('../../config/db.php');

// Function to fetch users from the database with doctor data
function getUsers($conn) {
    $query = "SELECT users.UserID, users.username, users.full_name, users.email, users.role, doctor.DoctorID 
              FROM users 
              LEFT JOIN doctor ON users.UserID = doctor.UserID";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle adding a user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Error: Username or Email already exists!";
    } else {
        $query = "INSERT INTO users (username, full_name, email, password, role) 
                VALUES ('$username', '$full_name', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $last_user_id = mysqli_insert_id($conn); // Get new user ID

            switch ($role) {
                case 'doctor':
                    $sql = "INSERT INTO doctor (UserID, DoctorName, Email) 
                            VALUES ('$last_user_id', '$full_name', '$email')";
                    break;
                case 'nurse':
                    $sql = "INSERT INTO nurse (UserID, Name, Email) 
                            VALUES ('$last_user_id', '$full_name', '$email')";
                    break;
                case 'pharmacist':
                    $sql = "INSERT INTO pharmacist (UserID, Name, Email) 
                            VALUES ('$last_user_id', '$full_name', '$email')";
                    break;
                case 'cashier':
                    $sql = "INSERT INTO cashier (UserID, Name) 
                            VALUES ('$last_user_id', '$full_name')";
                    break;
                case 'receptionist':
                    $sql = "INSERT INTO receptionist (UserID, Name, Email) 
                            VALUES ('$last_user_id', '$full_name', '$email')";
                    break;
                case 'admin':
                default:
                    $sql = "INSERT INTO admins (user_id) VALUES ('$last_user_id')";
                    break;
            }

            if (isset($sql)) {
                mysqli_query($conn, $sql);
            }

            header("Location: employees.php");
            exit();
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle deleting a user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = $_GET['delete'];

    // Fetch the role of the user to handle deletion from specific tables
    $user_query = "SELECT role FROM users WHERE UserID = '$user_id'";
    $user_result = mysqli_query($conn, $user_query);
    if (mysqli_num_rows($user_result) > 0) {
        $user_data = mysqli_fetch_assoc($user_result);
        $role = $user_data['role'];

        // Delete from the corresponding role table
        if ($role == 'doctor') {
            $delete_doctor_query = "DELETE FROM doctor WHERE UserID = '$user_id'";
            mysqli_query($conn, $delete_doctor_query);
        } elseif ($role == 'nurse') {
            $delete_nurse_query = "DELETE FROM nurse WHERE UserID = '$user_id'";
            mysqli_query($conn, $delete_nurse_query);
        } elseif ($role == 'pharmacist') {
            $delete_pharmacist_query = "DELETE FROM pharmacist WHERE UserID = '$user_id'";
            mysqli_query($conn, $delete_pharmacist_query);
        } elseif ($role == 'cashier') {
            $delete_cashier_query = "DELETE FROM cashier WHERE UserID = '$user_id'";
            mysqli_query($conn, $delete_cashier_query);
        }

        // Delete the user from the users table
        $delete_user_query = "DELETE FROM users WHERE UserID = '$user_id'";
        if (mysqli_query($conn, $delete_user_query)) {
            // Redirect back to the employees page after deletion
            header("Location: employees.php");
            exit();
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Error: User not found.";
    }
}

$users = getUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <style>
        .content { padding: 20px; }
        .form-container { margin-bottom: 20px; }
        .form-container input, .form-container select, .form-container button {
            margin: 5px 0; padding: 8px; width: 100%;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        tr:hover { background-color: #f5f5f5; }
    </style>
</head>
<body>

<div class="content">
    <h2>Employee Management</h2>

    <!-- Add User Form -->
    <div class="form-container">
        <form method="POST" action="employees.php">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="text" name="full_name" placeholder="Enter Full Name" required>
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="doctor">Doctor</option>
                <option value="nurse">Nurse</option>
                <option value="pharmacist">Pharmacist</option>
                <option value="cashier">Cashier</option>
                <option value="receptionist">Receptionist</option>
            </select>
            <button type="submit">Add User</button>
        </form>

        <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>
    </div>

    <!-- User Table -->
    <h3>User List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['UserID']; ?></td> <!-- Display UserID -->
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['full_name']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo ucfirst($user['role']); ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $user['UserID']; ?>">Edit</a> |
                <a href="employees.php?delete=<?php echo $user['UserID']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
    </table>
</div>

</body>
</html>

<?php
ob_end_flush();  // Flush output buffer
