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

// Handle adding a new booking
if (isset($_POST['add_booking'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $service = $_POST['service'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $animal = $_POST['animal'];

    header('Content-Type: application/json'); // Set the content type to JSON
    

    // Check for existing bookings with the same date and time
    $check_query = "SELECT * FROM bookings WHERE date = ? AND time = ?";
    $stmt_check = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt_check, "ss", $date, $time);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        // Booking already exists
        mysqli_stmt_close($stmt_check);
        echo json_encode(['status' => 'error', 'message' => 'Error: Booking already exists for the selected date and time.']);
        exit; // Exit after sending the response
    } else {
        // SQL query to insert the new booking
        $insert_query = "INSERT INTO bookings (first_name, last_name, phone, service, date, time, animal) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssss", $first_name, $last_name, $phone, $service, $date, $time, $animal);
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['status' => 'success', 'message' => 'Booking added successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error adding booking: ' . mysqli_stmt_error($stmt)]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error in insert query: ' . mysqli_error($conn)]);
        }
    }
    
    mysqli_stmt_close($stmt_check); // Close the check statement
    
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
// Check if the delete form has been submitted
if (isset($_POST['delete_booking_id'])) {
    $bookingIdToDelete = $_POST['delete_booking_id'];

    // Delete the booking record
    $deleteQuery = "DELETE FROM bookings WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt_delete, "i", $bookingIdToDelete);

    if (mysqli_stmt_execute($stmt_delete)) {
        echo "<script>alert('Booking deleted successfully.'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Error deleting booking: " . mysqli_stmt_error($stmt_delete) . "');</script>";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $animal = $_POST['animal'] ?? null; // Get the selected animal from the dropdown

    // Now you can insert $animal_to_save into the bookings table
    $stmt = $conn->prepare("INSERT INTO bookings (animal) VALUES (?)");
    $stmt->bind_param("s", $animal_to_save);

    if ($stmt->execute()) {
        echo "Booking recorded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

mysqli_close($conn); // Close the database connection
?>

<link rel="stylesheet" href="include/styles.css">

<style>
    body {
        background-color: #f8f9fa;
    }
    h2.display-4 {
        font-weight: bold;
        color: #9b111e;
    }
    .card {
        border-radius: 10px;
    }
    .mt-5 {
        margin-top: 3rem !important;
    }
    .table {
        margin: 20px 0;
        font-size: 1rem;
        border-radius: 8px;
        overflow: hidden;
    }
    .table thead {
        background-color: #343a40;
        color: #fff;
        text-align: center;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .table td, .table th {
        text-align: center;
        vertical-align: middle;
    }
    .table th:nth-last-child(2), .table td:nth-last-child(2),
    .table th:nth-last-child(1), .table td:nth-last-child(1) {
    width: 100px;
}
    .btn-circle {
        width: 60px;
        height: 35px;
        border-radius: 20px;
        font-size: 0.9rem;
        padding: 6px 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-success {
        background-color: #28a745;
        color: #fff;
        border: none;
    }
    .btn-danger {
        background-color: #dc3545;
        color: #fff;
        border: none;
    }
    .btn-success:hover {
        background-color: #218838;
    }
    .btn-danger:hover {
        background-color: #c82333;
    }
    .table td .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>

<div class="container mt-5">
    <div class="row bg-light shadow-sm rounded">
        <div class="col-12 p-4">
            <div class="text-center mb-4">
                <h2 class="display-4">Booking Slots</h2>
            </div>

<!-- Booking Form -->
<form method="POST" class="mb-4" style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); max-width: 700px; margin: 0 auto;">
    <h3 class="text-uppercase font-weight-bold mb-4 text-center" style="color: #7b1113;">Add New Booking</h3>
    
    <!-- Row for Name -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="first_name" style="color: #333; font-weight: bold;">First Name</label>
            <input type="text" class="form-control" name="first_name" required style="border: 1px solid #7b1113; border-radius: 8px; padding: 10px;">
        </div>
        <div class="form-group col-md-6">
            <label for="last_name" style="color: #333; font-weight: bold;">Last Name</label>
            <input type="text" class="form-control" name="last_name" required style="border: 1px solid #7b1113; border-radius: 8px; padding: 10px;">
        </div>
    </div>
    
    <!-- Row for Phone Number -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="phone" style="color: #333; font-weight: bold;">Phone Number</label>
            <input type="tel" class="form-control" name="phone" required style="border: 1px solid #7b1113; border-radius: 8px; padding: 10px;">
        </div>
    </div>
    
    <!-- Row for Service and Animal -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="service" style="color: #333; font-weight: bold;">Select the Service</label>
            <select class="form-control" name="service" id="service" required style="border: 1px solid #7b1113; border-radius: 8px; padding: 10px;">
                <option value="" disabled selected>Select a service</option>
                <option value="Grooming">Grooming</option>
                <option value="Vaccination">Vaccination</option>
                <option value="Dental Scaling">Dental Scaling</option>
                <option value="Boarding">Boarding</option>
                <option value="Consultation">Consultation</option>
                <option value="Surgery">Surgery</option>
                <option value="Pemandulan">Pemandulan</option>
                <option value="Outpatient Treatment">Outpatient Treatment</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="animal" style="color: #333; font-weight: bold;">Animal</label>
            <input type="text" class="form-control" name="animal" required style="border: 1px solid #7b1113; border-radius: 8px; padding: 10px;">
        </div>
    </div>
    
    <!-- Row for Date and Time -->
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="date" style="color: #333; font-weight: bold;">Booking Date</label>
            <input type="date" class="form-control" name="date" required style="border: 1px solid #7b1113; border-radius: 8px; padding: 10px;">
        </div>
        <div class="form-group col-md-6">
            <label for="time" style="color: #333; font-weight: bold;">Booking Time</label>
            <select class="form-control" name="time" id="time" required style="border: 1px solid #7b1113; border-radius: 8px; padding: 10px;">
                <option value="" disabled selected>Select Time</option>
                <option value="11:00">11:00 A.M</option>
                <option value="12:00">12:00 P.M</option>
                <option value="2:30">14:30 P.M</option>
                <option value="3:30">15:30 P.M</option>
                <option value="4:30">16:30 P.M</option>
                <option value="5:30">17:30 P.M</option>
            </select>
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="form-row justify-content-center">
        <div class="form-group col-md-6 text-center">
            <button type="submit" name="add_booking" class="btn btn-primary" 
                    style="background-color: #7b1113; border: none; border-radius: 8px; transition: background-color 0.3s; width: 100%; padding: 12px; font-size: 16px;">
                Add Booking
            </button>
        </div>
    </div>
</form>


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
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">S.No.</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Animal</th>
                        <th scope="col">Service</th>
                        <th scope="col">Booking Date</th>
                        <th scope="col">Booking Time</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                        <th scope="col">Other</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reset the pointer to the beginning of the result set
                    mysqli_data_seek($booking_result, 0);
                    $i = 1; // Initialize the serial number
                    while ($booking = mysqli_fetch_assoc($booking_result)) {
                        $customer_name = htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']);
                        $phone = htmlspecialchars($booking['phone']);
                        $animal = htmlspecialchars($booking['animal']);
                        $service = htmlspecialchars($booking['service']);
                        $booking_date = htmlspecialchars($booking['date']);
                        $booking_time = htmlspecialchars($booking['time']);
                        $status = htmlspecialchars($booking['status']);
                        $actions = htmlspecialchars($booking['actions']);
                    ?>
                        <tr>
                            <th><?php echo $i; ?></th>
                            <td><?php echo $customer_name; ?></td>
                            <td><?php echo $phone; ?></td>
                            <td><?php echo $animal; ?></td>  
                            <td><?php echo $service; ?></td>
                            <td><?php echo $booking_date; ?></td>
                            <td><?php echo $booking_time; ?></td>
                            <td id="status-<?php echo $booking['id']; ?>">
                                <?php if ($status === 'pending') { ?>
                                    <!-- Confirm and Reject buttons -->
                                    <button class="btn btn-circle btn-success confirm-btn" data-id="<?php echo $booking['id']; ?>">Confirm</button>
                                    <button class="btn btn-circle btn-danger reject-btn" data-id="<?php echo $booking['id']; ?>">Reject</button>
                                <?php } else { ?>
                                    <!-- Display the confirmed or rejected status -->
                                    <?php echo $status; ?>
                                <?php } ?>
                            </td>
                                <!--for actions-->
                            <td id="actions-<?php echo $booking['id']; ?>">
                                <?php if ($actions === 'pending') { ?>
                                    <!-- finish and cancel buttons -->
                                    <button class="btn btn-circle btn-success finish-btn" data-id="<?php echo $booking['id']; ?>">Finish</button>
                                    <button class="btn btn-circle btn-danger cancel-btn" data-id="<?php echo $booking['id']; ?>">Cancel</button>
                                <?php } else { ?>
                                    <!-- Display the confirmed or rejected status -->
                                    <?php echo $actions; ?>
                                <?php } ?>
                            </td>
                            
                            <td>
                                <!-- Delete Form -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-circle" onclick="return confirm('Are you sure you want to delete this booking?');">
                                        Delete
                                    </button>
                                </form>
                            </td>
                    <?php
                        $i++; // Increment the serial number
                    }
                    ?>
                </tbody>
            </table>
            <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Confirm button click
        document.querySelectorAll('.confirm-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var bookingId = this.getAttribute('data-id');
                updateStatusOrActions(bookingId, "status", "Confirmed");
            });
        });

        // Handle Reject button click
        document.querySelectorAll('.reject-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var bookingId = this.getAttribute('data-id');
                updateStatusOrActions(bookingId, "status", "Rejected");
            });
        });

        // Handle Finish button click
        document.querySelectorAll('.finish-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var bookingId = this.getAttribute('data-id');
                updateStatusOrActions(bookingId, "actions", "Finish");
            });
        });

        // Handle Cancel button click
        document.querySelectorAll('.cancel-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var bookingId = this.getAttribute('data-id');
                updateStatusOrActions(bookingId, "actions", "Cancel");
            });
        });

        // Function to update either status or actions via AJAX
        function updateStatusOrActions(bookingId, type, newValue) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText === 'Success') {
                        // Update the respective cell in the table
                        if (type === "status") {
                            document.getElementById('status-' + bookingId).innerHTML = newValue;
                        } else if (type === "actions") {
                            document.getElementById('actions-' + bookingId).innerHTML = newValue;
                        }
                    } else {
                        console.error('Failed to update:', xhr.responseText);
                        alert('Error updating. Please try again.');
                    }
                }
            };

            // Send the appropriate parameter based on the button clicked
            if (type === "status") {
                xhr.send('id=' + encodeURIComponent(bookingId) + '&status=' + encodeURIComponent(newValue));
            } else if (type === "actions") {
                xhr.send('id=' + encodeURIComponent(bookingId) + '&actions=' + encodeURIComponent(newValue));
            }
        }
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// JavaScript function to handle form submission
$('#bookingForm').on('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Gather form data
    let formData = $(this).serialize();

    $.ajax({
        type: 'POST',
        url: 'booking-slot.php', // Update this to the path of your PHP file
        data: formData,
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Successful',
                    text: response.message,
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Booking Failed',
                    text: response.message,
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'An error occurred. Please try again later.',
            });
        }
    });
});

</script>
        </div>
    </div>
</div>
<?php
require_once "include/footer.php"; // Include your footer file
?>