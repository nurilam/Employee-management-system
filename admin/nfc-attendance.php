<?php
require_once "../connection.php"; // Ensure you include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the UID from the NFC card (assumed to be sent via POST)
    $uid = $_POST['uid']; 

    // Step 1: Fetch the employee ID based on the UID
    $sql = "SELECT id FROM employee WHERE nfc_uid = '$uid'"; // Adjust the field name if necessary
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $employee = mysqli_fetch_assoc($result);
        $employee_id = $employee['id'];

        // Step 2: Check if the employee is clocking in or clocking out
        $current_time = date('Y-m-d H:i:s');
        
        // Check if there's already a clock-in record for today
        $sql_check = "SELECT * FROM attendance WHERE employee_id = $employee_id AND DATE(clock_in) = CURDATE() LIMIT 1";
        $check_result = mysqli_query($conn, $sql_check);

        if (mysqli_num_rows($check_result) == 0) {
            // Clock in
            $sql_insert = "INSERT INTO attendance (employee_id, clock_in) VALUES ($employee_id, '$current_time')";
            mysqli_query($conn, $sql_insert);
            echo json_encode(["status" => "success", "message" => "Clocked in at " . $current_time]);
        } else {
            // Clock out
            $sql_update = "UPDATE attendance SET clock_out = '$current_time' WHERE employee_id = $employee_id AND DATE(clock_in) = CURDATE()";
            mysqli_query($conn, $sql_update);
            echo json_encode(["status" => "success", "message" => "Clocked out at " . $current_time]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid NFC card."]);
    }
} else {
    // Handle cases where the request method is not POST
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
