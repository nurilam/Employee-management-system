<?php 
require_once "include/header.php";  // Include your header file
require_once "../connection.php";     // Include your database connection file

// Get the current date
$currentDate = date('Y-m-d');
$currentWeekStart = date('Y-m-d', strtotime('monday this week'));
$currentWeekEnd = date('Y-m-d', strtotime('sunday this week'));

// Get all bookings sorted by date
$select_bookings = "SELECT * FROM bookings ORDER BY date ASC, time ASC";  
$booking_result = mysqli_query($conn, $select_bookings);

// Check if the query was successful
if (!$booking_result) {
    die("Error fetching bookings: " . mysqli_error($conn));
}

// Initialize counters
$countToday = 0;
$countThisWeek = 0;
$countUpcoming = 0;

// Handle finish appointment request
if (isset($_POST['finish_appointment'])) {
    $booking_id = $_POST['booking_id'];

    // Delete the booking from the database
    $delete_query = "DELETE FROM bookings WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='alert alert-success'>Booking deleted successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting booking: " . mysqli_stmt_error($stmt) . "</div>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<div class='alert alert-danger'>Error in delete query: " . mysqli_error($conn) . "</div>";
    }
}

// Loop through bookings to calculate counts
while ($booking = mysqli_fetch_assoc($booking_result)) {
    $booking_date = $booking['date'];

    // Check if the booking is today
    if ($booking_date == $currentDate) {
        $countToday++;
    }

    // Check if the booking is in the current week
    if ($booking_date >= $currentWeekStart && $booking_date <= $currentWeekEnd) {
        $countThisWeek++;
    }

    // Check if the booking is upcoming (after this week)
    if ($booking_date > $currentWeekEnd) {
        $countUpcoming++;
    }
}

// Reset the booking result pointer for the next loop
mysqli_data_seek($booking_result, 0); // Rewind the result set

?>

<!-- Display counts of upcoming bookings -->
<div class="text-center mb-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm" style="background-color: #2e8b57; color: white;">
                <div class="card-body">
                    <h5 class="card-title">Today</h5>
                    <p class="card-text"><?php echo $countToday; ?> bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm" style="background-color: #1e90ff; color: white;">
                <div class="card-body">
                    <h5 class="card-title">This Week</h5>
                    <p class="card-text"><?php echo $countThisWeek; ?> bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm" style="background-color: #f4ca16; color: white;">
                <div class="card-body">
                    <h5 class="card-title">Upcoming</h5>
                    <p class="card-text"><?php echo $countUpcoming; ?> bookings</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row mt-5 bg-white shadow">
        <div class="col-12">
            <div class="text-center my-3">
                <h4>Booking Slots</h4>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr style="background-color: #ab4e52; color: black;">
                        <th scope="col">S.No.</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Service</th>
                        <th scope="col">Booking Date</th>
                        <th scope="col">Booking Time</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1; // Initialize the serial number
                    while ($booking = mysqli_fetch_assoc($booking_result)) {
                        $customer_name = htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); // Combine first and last name
                        $service = htmlspecialchars($booking['service']);
                        $booking_date = htmlspecialchars($booking['date']); // Use the correct column name
                        $booking_time = htmlspecialchars($booking['time']); // Use the correct column name
                        $status = "Confirmed"; // Assuming all bookings are confirmed for now
                    ?>
                    <tr>
                        <th><?php echo $i; ?></th>
                        <td><?php echo $customer_name; ?></td>
                        <td><?php echo $service; ?></td>
                        <td><?php echo $booking_date; ?></td>
                        <td><?php echo $booking_time; ?></td>
                        <td><?php echo ucfirst($status); ?></td>
                    </tr>
                    <?php
                    $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
require_once "include/footer.php"; // Include your footer file
?>
