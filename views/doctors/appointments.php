<?php
session_start();
include('../../config/db.php');
include('../../includes/doctor_header.php');
include('../../includes/doctor_sidebar.php');

$doctorName = '';
$patients = [];

if (isset($_SESSION['role']) && $_SESSION['role'] === 'doctor' && isset($_SESSION['role_id'])) {
    $doctorID = $_SESSION['role_id'];

    // Get Doctor Name
    $stmt = $conn->prepare("SELECT DoctorName FROM doctor WHERE DoctorID = ?");
    $stmt->bind_param("i", $doctorID);
    $stmt->execute();
    $stmt->bind_result($doctorName);
    $stmt->fetch();
    $stmt->close();

    // Handle POST actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       if (isset($_POST['confirm'])) {
            $patientName = $_POST['Name'];
            $patientAge = $_POST['Age'];
            $appointmentDate = $_POST['AppointmentDate'];
            $startTime = $_POST['AppointmentTime'];
            $startTimeObj = DateTime::createFromFormat('H:i:s', $startTime);
            $endTime = $startTimeObj->modify('+30 minutes')->format('H:i:s');



            // Get LocationID from doctor table
            $locQuery = $conn->prepare("SELECT LocationID FROM doctor WHERE DoctorID = ?");
            $locQuery->bind_param("i", $doctorID);
            $locQuery->execute();
            $locQuery->bind_result($locationID);
            $locQuery->fetch();
            $locQuery->close();

            if (!$locationID) {
                echo "DEBUG: LocationID is null. Cannot proceed.";
                exit;
            }


            // Insert into doctorschedule
            $insert = $conn->prepare("INSERT INTO doctorschedule 
                (DoctorID, LocationID, ScheduleDate, StartTime, EndTime, Status, PatientName, PatientAge)
                VALUES (?, ?, ?, ?, ?, 'Confirmed', ?, ?)");

            $insert->bind_param("iissssi", $doctorID, $locationID, $appointmentDate, $startTime, $endTime, $patientName, $patientAge);
            $insert->execute();
            $insert->close();

            $appointmentID = $_POST['AppointmentID'];

            $updateStatus = $conn->prepare("UPDATE appointments SET Status = 'Confirmed' WHERE AppointmentID = ?");
            $updateStatus->bind_param("i", $appointmentID);
            $updateStatus->execute();
            $updateStatus->close();

        }


        if (isset($_POST['reschedule'])) {
            $appointmentID = $_POST['AppointmentID'];
            $newDate = $_POST['newDate'];
            $newTime = $_POST['newTime'];

            $update = $conn->prepare("UPDATE appointments SET AppointmentDate = ?, AppointmentTime = ? WHERE AppointmentID = ?");
            $update->bind_param("ssi", $newDate, $newTime, $appointmentID);
            $update->execute();
            $update->close();
        }
    }

    // Get patients assigned to doctor
    $query = "SELECT a.AppointmentID, a.PatientID, a.Reason, a.AppointmentDate, a.AppointmentTime,
                    p.Name, p.Sex, p.DateOfBirth
              FROM appointments a
              INNER JOIN patients p ON a.PatientID = p.PatientID
              WHERE a.DoctorID = ? AND a.Status = 'Waiting'";


    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $doctorID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $dob = new DateTime($row['DateOfBirth']);
        $today = new DateTime();
        $age = $dob->diff($today)->y;

        $patients[] = [
            'AppointmentID' => $row['AppointmentID'],
            'PatientID' => $row['PatientID'],
            'Name' => $row['Name'],
            'Age' => $age,
            'Sex' => $row['Sex'],
            'Reason' => $row['Reason'],
            'Schedule' => $row['AppointmentDate'] . ' ' . $row['AppointmentTime']
        ];
    }
    $stmt->close();
}
?>

<!-- Main Content Area -->
<div class="content">
    <h2>Patients of Dr. <?= htmlspecialchars($doctorName) ?></h2>
    <div class="card-container">
        <?php foreach ($patients as $patient): ?>
            <div class="card">
            <form method="POST">
                <input type="hidden" name="Name" value="<?= htmlspecialchars($patient['Name']) ?>">
                <input type="hidden" name="Age" value="<?= htmlspecialchars($patient['Age']) ?>">
                <input type="hidden" name="AppointmentDate" value="<?= htmlspecialchars(explode(' ', $patient['Schedule'])[0]) ?>">
                <input type="hidden" name="AppointmentTime" value="<?= htmlspecialchars(explode(' ', $patient['Schedule'])[1]) ?>">

                <p class="patient-id">
                    Patient ID : <?= str_pad(htmlspecialchars($patient['PatientID']), 3, '0', STR_PAD_LEFT) ?>
                </p>

                    <input type="hidden" name="PatientID" value="<?= htmlspecialchars($patient['PatientID']) ?>">
                    <input type="hidden" name="AppointmentID" value="<?= htmlspecialchars($patient['AppointmentID']) ?>">

                    <label class="field-label">Name</label>
                    <div class="field-value"><?= htmlspecialchars($patient['Name']) ?></div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label">Age</label>
                            <div class="field-value"><?= htmlspecialchars($patient['Age']) ?></div>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Sex</label>
                            <div class="field-value"><?= htmlspecialchars($patient['Sex']) ?></div>
                        </div>
                    </div>


                    <label class="field-label">Condition or Reason</label>
                    <div class="field-value scrollable-text"><?= htmlspecialchars($patient['Reason']) ?></div>

                    <label class="field-label">Schedule</label>
                    <div class="schedule-row">
                        <div class="field-value"><?= htmlspecialchars(explode(' ', $patient['Schedule'])[0]) ?></div>
                        <div class="field-value">
                            <?= date("g:i A", strtotime(explode(' ', $patient['Schedule'])[1])) ?>
                        </div>
                    </div>
                    <div class="toggle-button">
                        <button type="button" class="confirm-btn" title="Confirm" onclick="confirmCard(this)">✔</button>
                        <button type="button" class="reschedule-btn" onclick="toggleReschedule(this)" title="Change Schedule">
                            🕒
                        </button>
                    </div>


                    <div class="reschedule" style="display: none; margin-top: 10px;">
                        <label class="field-label">New Date:</label>
                        <input type="date" name="newDate" required>
                        <label class="field-label">New Time:</label>
                        <input type="time" name="newTime" required>
                        <button type="submit" name="reschedule">Save</button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Styles -->
<style>
.content {
    margin-left: 250px;
    padding: 60px 40px 40px 40px;
    background-color: #e0f7fa;
    min-height: 100vh;
    box-sizing: border-box;
}

.card-container {
    display: flex;
    flex-wrap: wrap;
    gap: 70px;
}

.card {
    background-color: #9f2f56;
    border-radius: 20px;
    padding: 20px;
    width: 300px;
    color: white;
    font-family: Arial, sans-serif;
}

.card input {
    width: 100%;
    padding: 5px;
    margin-bottom: 10px;
    border-radius: 10px;
    border: none;
}

.reschedule {
    margin-top: 10px;
    background: #fff;
    color: #333;
    padding: 10px;
    border-radius: 10px;
}
.patient-id {
    text-align: center;
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 10px;
}
.field-label {
    font-size: 12px;
    color: #e0e0e0;
    display: block;
    margin-bottom: 3px;
}

.field-value {
    font-size: 16px;
    background-color: #ffffff33;
    padding: 5px 10px;
    border-radius: 8px;
    margin-bottom: 10px;
    text-align: center; /* new */
}

.scrollable-text {
    height: 3.6em;               
    overflow-y: auto;     
    overflow-x: hidden;         
    white-space: normal;    
    word-wrap: break-word;      
    line-height: 1.2em;
}

.schedule-row {
    display: flex;
    gap: 10px;
    justify-content: space-between;
}
.schedule-row .field-value {
    flex: 1;
    margin-bottom: 0; 
}

.toggle-button {
    display: flex;
    border-radius: 40px;
    overflow: hidden;
    width: 200px;
    margin: 20px auto 0;
    transition: background-color 0.4s ease;
}

.toggle-button .confirm-btn,
.toggle-button .reschedule-btn {
    flex: 1;
    padding: 12px 20px;
    border: none;
    cursor: pointer;
    font-size: 20px;
    color: black;
    background-color: transparent;
    transition: background-color 0.4s ease, color 0.3s ease;
}

/* Initial button colors */
.toggle-button .confirm-btn {
    background-color: #9fff9f;
}

.toggle-button .reschedule-btn {
    background-color: #ff9f9f;
}

/* Hover on confirm (✔): all turn green smoothly */
.toggle-button:hover .confirm-btn:hover ~ .reschedule-btn,
.toggle-button:hover .confirm-btn:hover {
    background-color: #9fff9f;
    transition: background-color 0.4s ease;
}

/* Hover on reschedule (🕒): all turn red smoothly */
.toggle-button:hover .reschedule-btn:hover ~ .confirm-btn,
.toggle-button:hover .reschedule-btn:hover {
    background-color: #ff9f9f;
    transition: background-color 0.4s ease;
}

.field-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
}

.field-group {
    flex: 1;
}

.field-group .field-value {
    text-align: center;
}
.card {
    transition: opacity 0.6s ease, transform 0.6s ease, margin 0.6s ease;
    position: relative;
}

.card.confirmed {
    background-color: #4caf50 !important; /* green */
}

.confirm-message {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: white;
    color: #4caf50;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 10px;
    font-size: 14px;
    animation: fadein 0.3s ease-in;
}

@keyframes fadein {
    from { opacity: 0; }
    to { opacity: 1; }
}


</style>

<!-- Script to Toggle Reschedule -->
<script>
function toggleReschedule(button) {
    const card = button.closest('.card');
    const section = card.querySelector('.reschedule');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}
</script>
<script>
function confirmCard(button) {
    const card = button.closest('.card');
    const form = card.querySelector('form');

    // Add hidden 'confirm' input so PHP detects it
    const confirmInput = document.createElement('input');
    confirmInput.type = 'hidden';
    confirmInput.name = 'confirm';
    confirmInput.value = '1';
    form.appendChild(confirmInput);

    // Add green background
    card.classList.add('confirmed');

    // Show 'Confirmed' label
    const confirmMsg = document.createElement('div');
    confirmMsg.className = 'confirm-message';
    confirmMsg.innerText = 'Confirmed';
    card.appendChild(confirmMsg);

    // Animate fade out after short delay
    setTimeout(() => {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
    }, 800);

    // Submit form after animation
    setTimeout(() => {
        form.submit();
    }, 1500);
}
</script>

</script>
