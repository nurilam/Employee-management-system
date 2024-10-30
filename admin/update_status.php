<?php
// Include your database connection
require_once "../connection.php"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $id = filter_input(INPUT_POST, 'id');
    $new_status = filter_input(INPUT_POST, 'status');

    // Verify parameters are valid
    if ($id && $new_status && in_array($new_status, ['Pending', 'Confirmed', 'Cancelled', 'Rejected'])) {
        // Update the status in the database
        $query = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $new_status, $id);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Error updating status";
        }
    } else {
        echo "Invalid parameters";
    }
}
?>
