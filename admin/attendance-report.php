<?php
require_once "include/header.php";
require_once "../connection.php";

// Set the correct timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

// Get the current date
$currentDate = date('Y-m-d');

if (isset($_POST['card_uid'])) {
    $cardUid = $_POST['card_uid'];

    // Fetch employee details using the card UID
    $employeeQuery = "SELECT id, name FROM employee WHERE card_uid = '$cardUid'";
    $employeeResult = mysqli_query($conn, $employeeQuery);

    if (mysqli_num_rows($employeeResult) > 0) {
        $employeeData = mysqli_fetch_assoc($employeeResult);
        $employeeId = $employeeData['id'];

        // Check if the employee has already clocked in today
        $attendanceCheck = "SELECT * FROM attendance WHERE employee_id = '$employeeId' AND DATE(clock_in) = '$currentDate'";
        $attendanceResult = mysqli_query($conn, $attendanceCheck);

        if (mysqli_num_rows($attendanceResult) > 0) {
            // Clock out
            $attendanceRecord = mysqli_fetch_assoc($attendanceResult);
            $attendanceId = $attendanceRecord['id'];

            // Check if the employee has already clocked out
            if ($attendanceRecord['clock_out'] === null) {
                // Update the clock-out time
                $updateQuery = "UPDATE attendance SET clock_out = NOW() WHERE id = '$attendanceId'";
                mysqli_query($conn, $updateQuery);
                echo "Clocked out successfully.";
            } else {
                echo "You have already clocked out today.";
            }
        } else {
            // Clock in
            $insertQuery = "INSERT INTO attendance (employee_id, clock_in) VALUES ('$employeeId', NOW())";
            mysqli_query($conn, $insertQuery);
            echo "Clocked in successfully.";
        }
    } else {
        echo "Invalid card. Please try again.";
    }
}

// Fetch all employees for the dropdown
$employeeQuery = "SELECT id, name FROM employee";
$employeeResult = mysqli_query($conn, $employeeQuery);

// Check if an employee and month are selected
$selectedEmployee = isset($_POST['employee']) ? $_POST['employee'] : '';
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('Y-m');

// Initialize variables for totals
$totalHoursWorked = 0;
$totalOvertimeMinutes = 0;

// Determine which SQL query to run
if ($selectedEmployee) {
    // Fetch attendance for the selected employee and month with total hours and overtime calculation
    $sql = "SELECT e.name, a.id AS attendance_id, a.clock_in, a.clock_out,
                   TIMEDIFF(a.clock_out, a.clock_in) AS hours_worked,
                   CASE 
                       WHEN TIMESTAMPDIFF(MINUTE, a.clock_in, a.clock_out) > 480 THEN TIMESTAMPDIFF(MINUTE, a.clock_in, a.clock_out) - 480 
                       ELSE 0 
                   END AS overtime_minutes
            FROM attendance a
            JOIN employee e ON a.employee_id = e.id
            WHERE a.employee_id = '$selectedEmployee' 
            AND DATE_FORMAT(a.clock_in, '%Y-%m') = '$selectedMonth'
            ORDER BY a.clock_in DESC";
} else {
    // Fetch attendance for the current day and count clock-ins and clock-outs
    $sql = "SELECT e.id, e.name, a.id AS attendance_id, a.clock_in, a.clock_out,
                   TIMEDIFF(a.clock_out, a.clock_in) AS hours_worked,
                   CASE 
                       WHEN TIMESTAMPDIFF(MINUTE, a.clock_in, a.clock_out) > 480 THEN TIMESTAMPDIFF(MINUTE, a.clock_in, a.clock_out) - 480 
                       ELSE 0 
                   END AS overtime_minutes
            FROM attendance a
            JOIN employee e ON a.employee_id = e.id
            WHERE DATE(a.clock_in) = '$currentDate'
            ORDER BY e.name";
}

$result = mysqli_query($conn, $sql);

// Handle record deletion
if (isset($_POST['delete_attendance_id'])) {
    $attendanceIdToDelete = $_POST['delete_attendance_id'];
    // Delete the attendance record
    $deleteQuery = "DELETE FROM attendance WHERE id = '$attendanceIdToDelete'";
    mysqli_query($conn, $deleteQuery);
    echo "Attendance record deleted successfully.";
}

// Handle record editing
if (isset($_POST['edit_attendance_id'])) {
    $attendanceIdToEdit = $_POST['edit_attendance_id'];
    $newClockIn = $_POST['new_clock_in'];
    $newClockOut = $_POST['new_clock_out'];

    // Update the attendance record with new clock-in and clock-out times
    $updateQuery = "UPDATE attendance SET clock_in = '$newClockIn', clock_out = '$newClockOut' WHERE id = '$attendanceIdToEdit'";
    mysqli_query($conn, $updateQuery);
    echo "Attendance record updated successfully.";
}
?>

<style>
    #table-search-users {
        display: block;
        width: 100%;
        padding: 10px;
        margin-top: 20px;
        font-size: 16px;
    }

    #myForm label {
        font-weight: bold;
        margin-bottom: 10px;
        display: block;
    }

    #myForm {
        width: 300px;
        margin: 0 auto;
    }
</style>

<div class="container">
    <h2>Attendance Clock-In/Out</h2>
    <form id="attendanceForm" method="POST" action="attendance-report.php">
        <!-- <label for="cardInput">Tap Your Proximity Card:</label>
        <input type="text" id="cardInput" name="card_uid" style="display:none" required>
        <button type="submit" style="display:none">Clock In/Out</button> -->

        <label for="table-search-users">Tap Your Proximity Card:</label>
        <input type="text" id="table-search-users" name="card_uid" style="display: none;" required>
        <button type="submit" style="display: none;">Clock In/Out</button>

    </form>

    <!-- <script>
        document.getElementById("cardInput").addEventListener("input", function() {
            this.form.submit();
        });
    </script> -->

    <script>
        function addInputToTable() {
            var inputField = document.getElementById('table-search-users');
            var form = document.getElementById('myForm');

            // Focus on the input field when page loads
            inputField.focus();

            inputField.addEventListener('input', function() {
                // Submit the form if input length is 10 characters
                if (this.value.length === 10) {
                    form.submit();
                }
            });

            inputField.addEventListener('paste', function(event) {
                setTimeout(function() {
                    if (inputField.value.length === 10) {
                        form.submit();
                    }
                }, 0);
            });
        }

        window.onload = function() {
            addInputToTable();
        };
    </script>
</div>

<div class="container">
    <h2>Attendance Report</h2>
    <p>Date: <?php echo date('l, F j, Y'); ?></p>

    <!-- Employee selection form -->
    <form method="POST">
        <label for="employee">Select Employee:</label>
        <select name="employee" id="employee" required>
            <option value="">-- Select Employee --</option>
            <?php while ($row = mysqli_fetch_assoc($employeeResult)) { ?>
                <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $selectedEmployee) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['name']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="month">Select Month:</label>
        <input type="month" name="month" id="month" value="<?php echo $selectedMonth; ?>" required>

        <button type="submit">View</button>
        <button type="button" onclick="window.print()">Print Report</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Hours Worked</th>
                <th>Overtime</th>
                <th>Action</th> <!-- New column for actions -->
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['clock_in']); ?></td>
                        <td><?php echo $row['clock_out'] ? htmlspecialchars($row['clock_out']) : 'Not clocked out'; ?></td>
                        <td>
                            <?php
                            // Display hours worked
                            if ($row['hours_worked']) {
                                list($h, $m) = explode(':', $row['hours_worked']);
                                $totalHoursWorked += (int)$h;
                                $totalRemainingMinutes = (int)$m;
                                echo htmlspecialchars($h) . ' hours ' . htmlspecialchars($m) . ' minutes';
                            } else {
                                echo '0 hours 0 minutes';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            // Display overtime minutes
                            $overtime_minutes = $row['overtime_minutes'];
                            $totalOvertimeMinutes += $overtime_minutes;
                            if ($overtime_minutes > 0) {
                                $overtime_hours = floor($overtime_minutes / 60);
                                $overtime_remaining_minutes = $overtime_minutes % 60;
                                echo htmlspecialchars($overtime_hours) . ' hours ' . $overtime_remaining_minutes . ' minutes';
                            } else {
                                echo '0 hours 0 minutes';
                            }
                            ?>
                        </td>
                        <td>
                            <!-- Deletion form for each record -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_attendance_id" value="<?php echo $row['attendance_id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                            <!-- Editing form for each record -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="edit_attendance_id" value="<?php echo $row['attendance_id']; ?>">
                                <input type="text" name="new_clock_in" value="<?php echo htmlspecialchars($row['clock_in']); ?>" required>
                                <input type="text" name="new_clock_out" value="<?php echo htmlspecialchars($row['clock_out']); ?>" required>
                                <button type="submit">Edit</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><?php echo $totalHoursWorked . ' hours'; ?></td>
                    <td><?php echo floor($totalOvertimeMinutes / 60) . ' hours ' . ($totalOvertimeMinutes % 60) . ' minutes'; ?></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="5">No attendance records found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require_once "include/footer.php"; ?>