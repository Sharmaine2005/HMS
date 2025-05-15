<div class="sidebar">
    <h2>Hospital System</h2>
    <ul>
        <li><a href="/HMS-main/views/admin/dashboard.php">Dashboard</a></li>
        <li>
            <a href="javascript:void(0);" class="dropdown-btn">Employees</a>
            <ul class="dropdown-content">
                <li><a href="/HMS-main/views/admin/employees.php">Employees Management</a></li> 
                <li style="text-align: center;">⎯⎯⎯⎯⎯⎯⎯⎯⎯⎯⎯⎯⎯⎯⎯⎯⎯</li>
                <li><a href="/HMS-main/views/admin/doctors.php">Doctors Management</a></li>
                <li><a href="/HMS-main/views/admin/nurses.php">Nurses Management</a></li>
                <li><a href="/HMS-main/views/admin/pharmacists.php">Pharmacists Management</a></li>
                <li><a href="/HMS-main/views/admin/cashiers.php">Cashiers Management</a></li>
            </ul>
        </li>

        <li><a href="/HMS-main/views/admin/billing.php">Billing Management</a></li>
        <li><a href="/HMS-main/views/admin/departments.php">Department Management</a></li>
        <li><a href="/HMS-main/views/admin/location.php">Location Management</a></li>
        <li><a href="/HMS-main/views/admin/reports.php">Reports</a></li>
        <li><a href="/HMS-main/views/admin/patients.php">Patients</a></li>
        <li><a href="/HMS-main/auth/logout.php">Logout</a></li>
    </ul>
</div>

<script>
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');
    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('active');
            const dropdownContent = btn.nextElementSibling;
            if (dropdownContent.style.display === 'block') {
                dropdownContent.style.display = 'none';
            } else {
                dropdownContent.style.display = 'block';
            }
        });
    });
</script>

<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #e0f7fa;
    color: #333;
    box-sizing: border-box;
    padding-top: 60px; /* Ensure body content starts below the header */
}

.sidebar {
    position: fixed;
    top: 60px; /* Push the sidebar below the header */
    width: 175px;
    height: calc(100vh - 60px); /* Subtract header height from full viewport height */
    background-color: #9c335a;
    padding: 20px;
    color: white;
    z-index: 1; /* Lower z-index so it stays behind the header */
    overflow-y: auto; /* To ensure the sidebar can scroll if content overflows */
}

.sidebar h2 {
    margin-bottom: 20px;
    font-size: 1.5em;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar ul li {
    margin-bottom: 10px;
    position: relative;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 8px;
    font-size: 1em;
}

.sidebar ul li a:hover {
    background-color: #7a0154;
    border-radius: 4px;
}

.content {
    margin-left: 220px; /* Ensure the content doesn't overlap with the sidebar */
    padding: 40px;
}

.dropdown-content {
    display: none;
    list-style-type: none;
    padding-left: 10px;
    background-color:#923f78;
}

.dropdown-content li a {
    padding: 6px 8px;
    font-size: 0.85em !important;
    opacity: 0.85;
    margin-left: 0px;
}

.sidebar .dropdown-content li a {
  padding-left: 30px !important;
}

.dropdown-btn::after {
    content: " ▼";
    font-size: 0.7em;
}

.dropdown-btn.active::after {
    content: " ▲";
}


</style>