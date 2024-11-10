<?php
// Include your database connection
require_once "../connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $new_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $new_actions = filter_input(INPUT_POST, 'actions', FILTER_SANITIZE_STRING);

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
    } elseif ($id && $new_actions && in_array($new_actions, ['Pending', 'Finish', 'Cancelled', 'Cancel'])) {
        // Update the actions in the database
        $query = "UPDATE bookings SET actions = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $new_actions, $id);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Error updating actions";
        }
    } else {
        echo "Invalid parameters";
    }
}
?>
