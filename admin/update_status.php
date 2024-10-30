<?php
// Include your database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $new_status = $_POST['status'];

    // Update the status in the database
    $query = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $new_status, $id);
    
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error updating status";
    }
}
?>
