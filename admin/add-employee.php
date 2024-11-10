<?php 
    require_once "include/header.php";
?>

<?php  
    $nameErr = $emailErr = $passErr = $salaryErr = $cardUidErr = $icNumberErr = $age = "";
    $name = $email = $dob = $gender = $pass = $salary = $card_uid = $ic_number = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handling Gender and Date of Birth
        if (empty($_REQUEST["gender"])) {
            $gender = ""; 
        } else {
            $gender = $_REQUEST["gender"];
        }

        if (empty($_REQUEST["dob"])) {
            $dob = "";
        } else {
            $dob = $_REQUEST["dob"];
            // Calculate age if DOB is provided
            $age = calculateAge($dob);
        }

        // Validating Name
        if (empty($_REQUEST["name"])) {
            $nameErr = "<p style='color:red'> * Name is required</p>";
        } else {
            $name = $_REQUEST["name"];
        }

        // Validating Salary
        if (empty($_REQUEST["salary"])) {
            $salaryErr = "<p style='color:red'> * Salary is required</p>";
            $salary = "";
        } else {
            $salary = $_REQUEST["salary"];
        }

        // Validating Email
        if (empty($_REQUEST["email"])) {
            $emailErr = "<p style='color:red'> * Email is required</p> ";
        } else {
            $email = $_REQUEST["email"];
        }

        // Validating Password
        if (empty($_REQUEST["pass"])) {
            $passErr = "<p style='color:red'> * Password is required</p> ";
        } else {
            $pass = $_REQUEST["pass"];
        }

        // Validating Card UID
        if (empty($_REQUEST["card_uid"])) {
            $cardUidErr = "<p style='color:red'> * Card UID is required</p>";
            $card_uid = "";
        } else {
            $card_uid = $_REQUEST["card_uid"];
        }

        // Validating IC Number
        if (empty($_REQUEST["ic_number"])) {
            $icNumberErr = "<p style='color:red'> * IC Number is required</p>";
        } else {
            $ic_number = $_REQUEST["ic_number"];
            // Check if ic_number is numeric and has exactly 12 digits
            if (!is_numeric($ic_number) || strlen($ic_number) != 12) {
                $icNumberErr = "<p style='color:red'> * IC Number must be a 12-digit number.</p>";
                $ic_number = ""; // Reset if invalid
            }
        }

        // Password validation regex
        if (!empty($pass) && !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $pass)) {
            $passErr = "<p style='color:red'> * Password must be at least 8 characters, contain one uppercase letter, one lowercase letter, one number, and one symbol.</p>";
        }

        // Check if everything is valid
        if (!empty($name) && !empty($email) && !empty($pass) && !empty($salary) && !empty($card_uid) && !empty($ic_number)) {
            // Database connection
            require_once "../connection.php";

            // Check if email already exists
            $sql_select_query = "SELECT email FROM employee WHERE email = '$email'";
            $r = mysqli_query($conn, $sql_select_query);

            if (mysqli_num_rows($r) > 0) {
                $emailErr = "<p style='color:red'> * Email Already Registered</p>";
            } else {
                // Insert data including card_uid and ic_number
                $sql = "INSERT INTO employee(name, email, password, dob, gender, salary, card_uid, ic_number) 
                        VALUES('$name', '$email', '$pass', '$dob', '$gender', '$salary', '$card_uid', '$ic_number')";

                $result = mysqli_query($conn, $sql);
                if ($result) {
                    // Reset form fields
                    $name = $email = $dob = $gender = $pass = $salary = $card_uid = $ic_number = "";

                    // Success message
                    echo "<script>
                        $(document).ready(function() {
                            $('#showModal').modal('show');
                            $('#modalHead').hide();
                            $('#linkBtn').attr('href', 'manage-employee.php');
                            $('#linkBtn').text('View Employees');
                            $('#addMsg').text('Employee Added Successfully!');
                            $('#closeBtn').text('Add More?');
                        });
                     </script>";
                }
            }
        }
    }

    // Function to calculate age from DOB
    function calculateAge($dob) {
        $birthDate = new DateTime($dob);
        $today = new DateTime('today');
        $age = $birthDate->diff($today)->y;
        return $age;
    }
?>

<div>
    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-4 shadow">
                                <h4 class="text-center">Add New Staff</h4>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

                                    <div class="form-group">
                                        <label>Full Name:</label>
                                        <input type="text" class="form-control" value="<?php echo $name; ?>" name="name">
                                        <?php echo $nameErr; ?>
                                    </div>

                                    <div class="form-group">
                                        <label>Email:</label>
                                        <input type="email" class="form-control" value="<?php echo $email; ?>" name="email">     
                                        <?php echo $emailErr; ?>
                                    </div>

                                    <div class="form-group">
                                        <label>Password:</label>
                                        <input type="password" class="form-control" value="<?php echo $pass; ?>" name="pass" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character."> 
                                        <?php echo $passErr; ?>           
                                    </div>

                                    <div class="form-group">
                                        <label>Salary:</label>
                                        <input type="number" class="form-control" value="<?php echo $salary; ?>" name="salary">  
                                        <?php echo $salaryErr; ?>            
                                    </div>

                                    <div class="form-group">
                                        <label>Card UID:</label>
                                        <input type="text" class="form-control" value="<?php echo $card_uid; ?>" name="card_uid" placeholder="Enter Card UID">
                                        <?php echo $cardUidErr; ?>
                                    </div>

                                    <div class="form-group">
                                        <label>IC Number:</label>
                                        <input type="text" class="form-control" value="<?php echo $ic_number; ?>" name="ic_number" placeholder="Enter IC Number">
                                        <?php echo $icNumberErr; ?>
                                    </div>

                                    <div class="form-group">
                                        <label>Date-of-Birth:</label>
                                        <input type="date" class="form-control" value="<?php echo $dob; ?>" name="dob">  
                                    </div>

                                    <?php if ($dob) { ?>
                                        <div class="form-group">
                                            <label>Age:</label>
                                            <input type="text" class="form-control" value="<?php echo $age; ?>" readonly>
                                        </div>
                                    <?php } ?>

                                    <div class="form-group form-check form-check-inline">
                                        <label class="form-check-label">Gender:</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" <?php if ($gender == "Male") { echo "checked"; } ?> value="Male">
                                        <label class="form-check-label">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" <?php if ($gender == "Female") { echo "checked"; } ?> value="Female">
                                        <label class="form-check-label">Female</label>
                                    </div>

                                    <br>

                                    <button type="submit" class="btn btn-primary btn-block" style="background-color: #0047ab; border-color: #7b1113;">Add</button>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once "include/footer.php";
?>
