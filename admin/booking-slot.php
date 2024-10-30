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

// Check if the request is an AJAX request
if (isset($_POST['finish_appointment_ajax'])) {
    $booking_id = $_POST['booking_id'];

    // Delete the booking from the database
    $delete_query = "DELETE FROM bookings WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        if (mysqli_stmt_execute($stmt)) {
            // Return success response
            echo json_encode(['status' => 'success']);
        } else {
            // Return error response
            echo json_encode(['status' => 'error', 'message' => mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}


// Handle adding a new booking
if (isset($_POST['add_booking'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $service = $_POST['service'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $animal = $_POST['animal'];

    // Check for existing bookings with the same date and time
    $check_query = "SELECT * FROM bookings WHERE date = ? AND time = ?";
    $stmt_check = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt_check, "ss", $date, $time);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "Error: Booking already exists for the selected date and time.";
        mysqli_stmt_close($stmt_check);
    } else {
        // SQL query to insert the new booking
        $insert_query = "INSERT INTO bookings (first_name, last_name, service, date, time, animal) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $last_name, $service, $date, $time, $animal);
            if (mysqli_stmt_execute($stmt)) {
                echo "Booking added successfully.";
            } else {
                echo "Error adding booking: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error in insert query: " . mysqli_error($conn);
        }
    }

    mysqli_stmt_close($stmt_check); // Close the check statement
    header("Location: booking-slot.php" . $_SERVER['PHP_SELF']); // Redirect to the same page
    exit;
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

mysqli_close($conn); // Close the database connection
?>

<style>
<link rel="stylesheet" href="include/styles.css">
body {
    background-color: #f8f9fa;
}
h2.display-4 {
    font-weight: bold;
    color: #9b111e; /* Maroon color for headings */
}
.card {
    border-radius: 10px;
}
.table {
    border-radius: 10px;
    overflow: hidden;
}
.table th, .table td {
    vertical-align: middle;
}
/* Hover effect for table rows */
.table-hover tbody tr:hover {
    background-color: #e9ecef;
}
/* Margin adjustments */
.mt-5 {
    margin-top: 3rem !important;
}
.custom-button {
    padding: 10px 15px; /* Adjust the padding as needed */
    font-size: 10px;    /* Adjust the font size */
}

</style>

<div class="container mt-5">
    <div class="row bg-light shadow-sm rounded">
        <div class="col-12 p-4">
            <div class="text-center mb-4">
                <h2 class="display-4">Booking Slots</h2>
            </div>

            <!-- Booking Form -->
<form method="post" class="mb-4" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <h5 class="text-uppercase font-weight-bold mb-4" style="color: #7b1113;">Add New Booking</h5>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" name="first_name" required style="border: 1px solid #7b1113; border-radius: 8px;">
        </div>
        <div class="form-group col-md-6">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" name="last_name" required style="border: 1px solid #7b1113; border-radius: 8px;">
        </div>
    </div>
    <div class="form-row">
    <div class="form-group col-md-6">
    <label for="service">Select the Service</label>
    <select class="form-control" name="service" id="service" required style="border: 1px solid #7b1113; border-radius: 8px;">
        <option value="" disabled selected>Select a service</option>
        <option value="Grooming">Grooming</option>
        <option value="Vaccination">Vaccination</option>
        <option value="Dental Scaling">Dental Scaling</option>
        <option value="Boarding">Boarding</option>
        <option value="Consultation">Consultation</option>
        <option value="Surgery">Surgery</option>
        <option value="Pemandulan">Pemandulan</option>
        <option value="Other Services">Other Services</option>
    </select>
</div>

<div class="form-group col-md-6" id="otherServiceGroup" style="display:none;">
    <label for="otherService">Please specify the service</label>
    <input type="text" class="form-control" name="other_service" id="otherService" placeholder="Enter the service you need" style="border: 1px solid #7b1113; border-radius: 8px;">
</div>

<script>
    document.getElementById('service').addEventListener('change', function() {
        var otherServiceGroup = document.getElementById('otherServiceGroup');
        if (this.value === 'Other Services') {
            otherServiceGroup.style.display = 'block';
            document.getElementById('otherService').required = true;
        } else {
            otherServiceGroup.style.display = 'none';
            document.getElementById('otherService').required = false;
        }
    });
</script>

<div class="form-group col-md-6">
    <label for="animal">Select the Animal</label>
    <select class="form-control" name="animal" id="animal" required style="border: 1px solid #7b1113; border-radius: 8px;">
        <option value="" disabled selected>Select an animal</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
        <option value="Bird">Bird</option>
        <option value="Rabbit">Rabbit</option>
        <option value="Other">Other</option>
    </select>
</div>

<div class="form-group col-md-6" id="otherAnimalGroup" style="display:none;">
    <label for="otherAnimal">Please specify the animal</label>
    <input type="text" class="form-control" name="other_animal" id="otherAnimal" placeholder="Enter the animal type" style="border: 1px solid #7b1113; border-radius: 8px;">
</div>

<script>
    document.getElementById('animal').addEventListener('change', function() {
        var otherAnimalGroup = document.getElementById('otherAnimalGroup');
        if (this.value === 'Other') {
            otherAnimalGroup.style.display = 'block';
            document.getElementById('otherAnimal').required = true;
        } else {
            otherAnimalGroup.style.display = 'none';
            document.getElementById('otherAnimal').required = false;
        }
    });
</script>

    </div>
    <div class="form-row">
    <div class="form-group col-md-6">
        <label for="date">Booking Date</label>
        <input type="date" class="form-control" name="date" required style="border: 1px solid #7b1113; border-radius: 8px;">
    </div>
    <div class="form-group col-md-6">
        <label for="time">Booking Time</label>
        <input type="time" class="form-control" name="time" required style="border: 1px solid #7b1113; border-radius: 8px;">
    </div>
</div>
<div class="form-row justify-content-center">
    <div class="form-group col-md-6 text-center">
        <button type="submit" name="add_booking" class="btn btn-primary" style="background-color: #7b1113; border: none; border-radius: 8px; transition: background-color 0.3s; width: 100%;">
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
                        <th scope="col">Service</th>
                        <th scope="col">Booking Date</th>
                        <th scope="col">Booking Time</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th> 
                        <!-- New Actions Column -->
                    </tr>
                </thead>
                <tbody>
                <?php 
    // Reset the pointer to the beginning of the result set
    mysqli_data_seek($booking_result, 0);
    $i = 1; // Initialize the serial number
    while ($booking = mysqli_fetch_assoc($booking_result)) {
        $customer_name = htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']);
        $service = htmlspecialchars($booking['service']);
        $booking_date = htmlspecialchars($booking['date']);
        $booking_time = htmlspecialchars($booking['time']);
        $status = htmlspecialchars($booking['status']); // Assume status is fetched from the database
?>
<tr>
    <th><?php echo $i; ?></th>
    <td><?php echo $customer_name; ?></td>
    <td><?php echo $service; ?></td>
    <td><?php echo $booking_date; ?></td>
    <td><?php echo $booking_time; ?></td>
    <td id="status-<?php echo $booking['id']; ?>">
        <?php if ($status === 'pending') { ?>
            <!-- Confirm and Reject buttons -->
            <button class="btn btn-success confirm-btn" data-id="<?php echo $booking['id']; ?>">Confirm</button>
            <button class="btn btn-danger reject-btn" data-id="<?php echo $booking['id']; ?>">Reject</button>
        <?php } else { ?>
            <!-- Display the confirmed or rejected status -->
            <?php echo $status; ?>
        <?php } ?>
    </td>
    <td>
    <!-- Finish Appointment Button -->
    <button class="btn btn-success custom-button finish-button" data-id="<?php echo $booking['id']; ?>">Finish Appointment</button>
</td>
</tr>



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
            updateStatus(bookingId, 'Confirmed');
        });
    });

    // Handle Reject button click
    document.querySelectorAll('.reject-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var bookingId = this.getAttribute('data-id');
            updateStatus(bookingId, 'Rejected');
        });
    });

    // Function to update the status via AJAX
    function updateStatus(bookingId, newStatus) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Update the status in the table
                document.getElementById('status-' + bookingId).innerHTML = newStatus;
            }
        };
        xhr.send('id=' + bookingId + '&status=' + newStatus);
    }
});

            </script>
                <script>
    // Handle Finish Appointment Button Click
    document.querySelectorAll('.finish-button').forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-id');
            if (confirm("Are you sure you want to finish this appointment?")) {
                // Perform AJAX request to delete the booking
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "<?php echo $_SERVER['PHP_SELF']; ?>", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            alert("Appointment finished successfully.");
                            location.reload();  // Reload the page to update the list
                        } else {
                            alert("Error finishing appointment: " + response.message);
                        }
                    }
                };
                xhr.send("finish_appointment_ajax=1&booking_id=" + bookingId);
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
