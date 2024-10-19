<?php
require_once "../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id']; // This should be the UID of the NFC card
    $current_time = date('Y-m-d H:i:s');

    // Check if the employee exists based on NFC UID
    $sql = "SELECT id FROM employee WHERE nfc_uid = '$employee_id'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $employee = mysqli_fetch_assoc($result);
        $emp_id = $employee['id'];

        // Update clock-out time
        $sql_update = "UPDATE attendance SET clock_out = '$current_time' WHERE employee_id = '$emp_id' AND clock_out IS NULL";
        mysqli_query($conn, $sql_update);
        echo "Clock-out successful for employee ID: $emp_id";
    } else {
        echo "Employee not found.";
    }
}
?>
