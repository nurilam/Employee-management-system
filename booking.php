<?php
// Include database connection file
include 'connection.php'; // Make sure to adjust the path if needed

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $service = $_POST['service'];
    $animal = $_POST['animal'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Insert data into the database
    $sql = "INSERT INTO bookings (first_name, last_name, phone, service, animal, date, time)
            VALUES ('$first_name', '$last_name', '$phone', '$service', '$animal', '$date', '$time')";

    if (mysqli_query($conn, $sql)) {
        $message = "Booking successfully created!";
        $messageType = "success";
    } else {
        $message = "Error: " . mysqli_error($conn);
        $messageType = "error";
    }

    mysqli_close($conn); // Close the database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Slot</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Potta+One&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f9f3e3;
            background-image: url('bglogin.png');
            background-size: cover;
            background-position: center;
            font-family: 'Potta One', cursive;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        h1 {
            text-align: center;
            color: white;
            font-size: 35px;
            margin-top: 20px;
        }

        form {
            background-color: #800000;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .form-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group .form-item {
            flex: 1 1 48%;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #f9f3e3;
        }

        input[type="text"],
        input[type="tel"],
        input[type="date"],
        select {
            width: calc(100% - 20px);
            padding: 10px 20px;
            margin-bottom: 15px;
            border: 2px solid #002147;
            border-radius: 8px;
            background-color: #f9f3e3;
            font-family: 'Potta One', cursive;
            color: #800000;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #f9f3e3; /* Maroon button */
            color: #800000;
            border: 2px solid #002147;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 18px;
            width: 100%;
            font-family: 'Potta One', cursive;
        }

        input[type="submit"]:hover {
            background-color: #a02828; /* Darker maroon on hover */
        }
    </style>
</head>
<body>
    
<form method="POST" action="">
    <h1>
        <span>Dr.Dann Animal Clinic</span><br>
        <span>Booking Slot Form</span>
    </h1>
    
    <div class="form-group">
        <div class="form-item">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        <div class="form-item">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
    </div>

    <div class="form-group">
        <div class="form-item">
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>
        </div>
    </div>

    <div class="form-group">
        <div class="form-item">
            <label for="service">Service:</label>
            <select class="form-control" name="service" id="service" required>
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
        <div class="form-item">
            <label for="animal">Animal:</label>
            <input type="text" id="animal" name="animal" required>
        </div>
        </div>
    



    <div class="form-group">
        <div class="form-item">
            <label for="date">Booking Date:</label>
            <input type="date" id="date" name="date" required>
        </div>

        <div class="form-item">
            <label for="time">Booking Time:</label>
            <select name="time" id="time" required>
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

    <input type="submit" value="Submit Booking">
</form>

    <script>
        // Display SweetAlert2 message based on PHP variables
        <?php if (!empty($message)): ?>
            Swal.fire({
                icon: '<?php echo $messageType; ?>',
                title: '<?php echo $messageType === "success" ? "Success" : "Error"; ?>',
                text: '<?php echo $message; ?>',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</body>
</html>
