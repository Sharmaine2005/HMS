<?php
ob_start();
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
include('../../config/db.php');

function getUsers($conn) {
    $query = "SELECT users.UserID, users.username, users.full_name, users.email, users.role FROM users";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
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
            $last_user_id = mysqli_insert_id($conn);
            switch ($role) {
                case 'doctor':
                    $sql = "INSERT INTO doctor (UserID, DoctorName, Email) VALUES ('$last_user_id', '$full_name', '$email')";
                    break;
                case 'nurse':
                    $sql = "INSERT INTO nurse (UserID, Name, Email) VALUES ('$last_user_id', '$full_name', '$email')";
                    break;
                case 'pharmacist':
                    $sql = "INSERT INTO pharmacist (UserID, Name, Email) VALUES ('$last_user_id', '$full_name', '$email')";
                    break;
                case 'cashier':
                    $sql = "INSERT INTO cashier (UserID, Name) VALUES ('$last_user_id', '$full_name')";
                    break;
                case 'receptionist':
                    $sql = "INSERT INTO receptionist (UserID, Name, Email) VALUES ('$last_user_id', '$full_name', '$email')";
                    break;
                case 'admin':
                default:
                    $sql = "INSERT INTO admins (user_id) VALUES ('$last_user_id')";
                    break;
            }
            if (isset($sql)) mysqli_query($conn, $sql);
            header("Location: employees.php");
            exit();
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $user_id = mysqli_real_escape_string($conn, $_POST['edit_user_id']);
    $username = mysqli_real_escape_string($conn, $_POST['edit_username']);
    $full_name = mysqli_real_escape_string($conn, $_POST['edit_full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['edit_email']);
    $role = mysqli_real_escape_string($conn, $_POST['edit_role']);

    $query = "UPDATE users SET username='$username', full_name='$full_name', email='$email', role='$role' WHERE UserID='$user_id'";
    mysqli_query($conn, $query);
    header("Location: employees.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $role_query = "SELECT role FROM users WHERE UserID = '$user_id'";
    $role_result = mysqli_query($conn, $role_query);
    if (mysqli_num_rows($role_result) > 0) {
        $role = mysqli_fetch_assoc($role_result)['role'];
        switch ($role) {
            case 'doctor':
                mysqli_query($conn, "DELETE FROM doctor WHERE UserID = '$user_id'");
                break;
            case 'nurse':
                mysqli_query($conn, "DELETE FROM nurse WHERE UserID = '$user_id'");
                break;
            case 'pharmacist':
                mysqli_query($conn, "DELETE FROM pharmacist WHERE UserID = '$user_id'");
                break;
            case 'cashier':
                mysqli_query($conn, "DELETE FROM cashier WHERE UserID = '$user_id'");
                break;
            case 'receptionist':
                mysqli_query($conn, "DELETE FROM receptionist WHERE UserID = '$user_id'");
                break;
            case 'admin':
                mysqli_query($conn, "DELETE FROM admins WHERE user_id = '$user_id'");
                break;
        }
        mysqli_query($conn, "DELETE FROM users WHERE UserID = '$user_id'");
        header("Location: employees.php");
        exit();
    }
}

$users = getUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Employee Management</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <style>
            
         body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
        }

        .content {
            display: flex;
            gap: 20px;
            
        }

        /* Left column (list) takes the remaining width minus the fixed right column */
        .left-column {
            flex: 1 1 auto;
            margin-right: 320px; /* Reserve space for fixed right column */
        }
        .right-column {
            position: fixed;
            right: 0px; /* distance from right edge */
            top: 80px;   /* distance from top (adjust if you have a header) */
            width: 300px;
            background: rgb(226, 136, 173);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            height: auto;
        }

.right-column::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 1.5px;
    background-color: #ccc;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center; /* from first style, centered text */
}

th {
    background-color: #f8f9fa;
}

tr:hover {
    background-color: #f5f5f5;
}

.form-container input,
.form-container select,
.form-container button {
    margin: 10px 0;
    padding: 8px;
    width: 100%;
    box-sizing: border-box;
    font-size: 14px;
}

.form-container label {
    display: block;
    margin-top: 15px;
    font-weight: 600;
}

.right-column h2 {
    margin-top: 20px;
    margin-bottom: 20px;
}

.delete-link {
    color: red;
}

form input, form button {
    padding: 5px 10px;
    margin-top: 5px;
}

/* Buttons from first style */
button.view-btn {
    background-color: #6f42c1;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    cursor: pointer;
}

button.view-btn:hover {
    background-color: #512da8;
}

/* Modal styles */
.modal {
    position: fixed;
    z-index: 999;
    left: 0; top: 0;
    width: 100%; height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
    display: none;
    justify-content: center;
    align-items: center;
}

.modal-content {
    border: 2px solid purple;
    border-radius: 12px;
    padding: 40px;
    background-color: #fff;
    max-width: 500px;
    width: 90%;
    text-align: center;
    box-shadow: 0 0 12px rgba(0,0,0,0.05);
    position: relative;
}

.close {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 28px;
    font-weight: bold;
    color: #888;
    cursor: pointer;
}

.close:hover {
    color: #000;
}

.profile-img {
    width: 100px;
    height: 100px;
    margin: 0 auto 30px;
    border-radius: 50%;
    background-color: #f0f0f0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.profile-img img {
    width: 60px;
    height: 60px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin: 12px 0;
    font-size: 16px;
    color: #555;
}

.info-row strong {
    font-weight: 600;
    color: #444;
}

.back-link {
    display: inline-block;
    margin-top: 30px;
    text-decoration: none;
    color: #fff;
    background-color: #6f42c1;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
}

.back-link:hover {
    background-color: #512da8;
}

        
    </style>
</head>
<body>
<div class="content">
    <div class="left-column">
        <h2>User List</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['UserID'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($user['role'])) ?></td>
                    <td>
                        <a href="javascript:void(0);" class="edit-btn"
                           data-id="<?= $user['UserID'] ?>"
                           data-username="<?= htmlspecialchars($user['username']) ?>"
                           data-full_name="<?= htmlspecialchars($user['full_name']) ?>"
                           data-email="<?= htmlspecialchars($user['email']) ?>"
                           data-role="<?= htmlspecialchars($user['role']) ?>">Edit</a> |
                        <a href="employees.php?delete=<?= $user['UserID'] ?>" 
                            onclick="return confirm('Delete this user?');" 
                            class="delete-link">Delete</a>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="right-column">
        <h2>Add Employee</h2>
        <div class="form-container">
            <form method="POST" action="employees.php">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter Username" required>

                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter Full Name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter Email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter Password" required>

                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="doctor">Doctor</option>
                    <option value="nurse">Nurse</option>
                    <option value="pharmacist">Pharmacist</option>
                    <option value="cashier">Cashier</option>
                </select>

                <button type="submit">Add User</button>
            </form>
            <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#f9f9f9; padding:15px; box-shadow:0 0 8px rgba(0,0,0,0.2); z-index:999; font-size:14px; width:350px;">
    <form method="POST" action="employees.php">
        <input type="hidden" name="edit_user_id" id="edit_user_id">
        <div><label>Username</label>
            <input type="text" name="edit_username" id="edit_username" required style="width:100%;"></div>
        <div><label>Full Name</label>
            <input type="text" name="edit_full_name" id="edit_full_name" required style="width:100%;"></div>
        <div><label>Email</label>
            <input type="email" name="edit_email" id="edit_email" required style="width:100%;"></div>
        <div><label>Role</label>
            <select name="edit_role" id="edit_role" required style="width:100%;">
                <option value="admin">Admin</option>
                <option value="doctor">Doctor</option>
                <option value="nurse">Nurse</option>
                <option value="pharmacist">Pharmacist</option>
                <option value="cashier">Cashier</option>
            </select>
        </div>
        <div style="margin-top:10px; text-align:right;">
            <button type="submit" name="update_user" style="padding:5px 10px;">Update</button>
            <button type="button" onclick="document.getElementById('editModal').style.display='none'" style="padding:5px 10px;">Cancel</button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('edit_user_id').value = this.dataset.id;
        document.getElementById('edit_username').value = this.dataset.username;
        document.getElementById('edit_full_name').value = this.dataset.full_name;
        document.getElementById('edit_email').value = this.dataset.email;
        document.getElementById('edit_role').value = this.dataset.role;
        document.getElementById('editModal').style.display = 'block';
    });
});
</script>

</body>
</html>

<?php ob_end_flush(); ?>