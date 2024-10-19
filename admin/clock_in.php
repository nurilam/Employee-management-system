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

        // Insert clock-in record
        $sql_insert = "INSERT INTO attendance (employee_id, clock_in) VALUES ('$emp_id', '$current_time')";
        mysqli_query($conn, $sql_insert);
        echo "Clock-in successful for employee ID: $emp_id";
    } else {
        echo "Employee not found.";
    }
}
?>
