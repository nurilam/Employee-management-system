<?php
require_once "include/header.php";
?>

<?php 

$id = $_GET["id"];
require_once "../connection.php";

// Get employee data from the database
$sql = "SELECT * FROM employee WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $name = $row["name"];
    $email = $row["email"];
    $dob = $row["dob"];
    $gender = $row["gender"];
    $salary = $row["salary"];
    $card_uid = $row["card_uid"];
    $ic_number = $row["ic_number"]; // Fetch IC number from database
}

$nameErr = $emailErr = $passErr = $salaryErr = $card_uidErr = $ic_numberErr = "";
$pass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Gender and DOB
    $gender = $_REQUEST["gender"] ?? "";
    $dob = $_REQUEST["dob"] ?? "";

    // Name validation
    if (empty($_REQUEST["name"])) {
        $nameErr = "<p style='color:red'> * Name is required</p>";
        $name = "";
    } else {
        $name = $_REQUEST["name"];
    }

    // Salary validation
    if (empty($_REQUEST["salary"])) {
        $salaryErr = "<p style='color:red'> * Salary is required</p>";
        $salary = "";
    } else {
        $salary = $_REQUEST["salary"];
    }

    // Email validation
    if (empty($_REQUEST["email"])) {
        $emailErr = "<p style='color:red'> * Email is required</p>";
        $email = "";
    } else {
        $email = $_REQUEST["email"];
    }

    // Password validation
    if (empty($_REQUEST["pass"])) {
        $passErr = "<p style='color:red'> * Password is required</p>";
    } else {
        $pass = $_REQUEST["pass"];
    }

    // Card UID validation
    if (empty($_REQUEST["card_uid"])) {
        $card_uidErr = "<p style='color:red'> * Card UID is required</p>";
    } else {
        $card_uid = $_REQUEST["card_uid"];
    }

    // IC Number validation
    if (empty($_REQUEST["ic_number"])) {
        $ic_numberErr = "<p style='color:red'> * IC Number is required</p>";
    } else {
        $ic_number = $_REQUEST["ic_number"];
    }

    // Validate password strength
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $pass)) {
        $passErr = "<p style='color:red'> * Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.</p>";
        $pass = "";
    }
}

// Only proceed if all fields are filled
if (!empty($name) && !empty($email) && !empty($pass) && !empty($salary) && !empty($card_uid) && !empty($ic_number)) {
    
    // Check if the user is changing their email
    if ($email != $row["email"]) {  // Compare with the original email from the database
        $sql_select_query = "SELECT email FROM employee WHERE email = '$email'";
        $r = mysqli_query($conn, $sql_select_query);

        // If the email already exists, show error
        if (mysqli_num_rows($r) > 0) {
            $emailErr = "<p style='color:red'> * Email Already Registered</p>";
        } else {
            // Proceed with the update
            $sql = "UPDATE employee SET name = '$name', email = '$email', password = '$pass', dob = '$dob', gender = '$gender', salary = '$salary', card_uid = '$card_uid', ic_number = '$ic_number' WHERE id = $id";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo "<script>
                    $(document).ready(function(){
                        $('#showModal').modal('show');
                        $('#modalHead').hide();
                        $('#linkBtn').attr('href', 'manage-employee.php');
                        $('#linkBtn').text('View Employees');
                        $('#addMsg').text('Profile Edited Successfully!');
                        $('#closeBtn').text('Edit Again?');
                    });
                </script>";
            }
        }
    } else {
        // If the email is not being changed, just update the other details
        $sql = "UPDATE employee SET name = '$name', password = '$pass', dob = '$dob', gender = '$gender', salary = '$salary', card_uid = '$card_uid', ic_number = '$ic_number' WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            echo "<script>
                $(document).ready(function(){
                    $('#showModal').modal('show');
                    $('#modalHead').hide();
                    $('#linkBtn').attr('href', 'manage-employee.php');
                    $('#linkBtn').text('View Employees');
                    $('#addMsg').text('Profile Edited Successfully!');
                    $('#closeBtn').text('Edit Again?');
                });
            </script>";
        }
    }
}
?>

<div style=""> 
<div class="login-form-bg h-100">
        <div class="container  h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-4 shadow">                       
                                <h4 class="text-center">Edit Employee Profile</h4>
                                <form method="POST" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                                
                                <div class="form-group">
                                    <label>Full Name :</label>
                                    <input type="text" class="form-control" value="<?php echo $name; ?>" name="name">
                                    <?php echo $nameErr; ?>
                                </div>

                                <div class="form-group">
                                    <label>Email :</label>
                                    <input type="email" class="form-control" value="<?php echo $email; ?>" name="email">     
                                    <?php echo $emailErr; ?>
                                </div>

                                <div class="form-group">
                                    <label>Password: </label>
                                    <input type="password" class="form-control" value="<?php echo $pass; ?>" name="pass"> 
                                    <?php echo $passErr; ?>           
                                </div>

                                <div class="form-group">
                                    <label>Salary :</label>
                                    <input type="number" class="form-control" value="<?php echo $salary; ?>" name="salary">  
                                    <?php echo $salaryErr; ?>            
                                </div>

                                <div class="form-group">
                                    <label>Card UID :</label>
                                    <input type="text" class="form-control" value="<?php echo $row['card_uid']; ?>" name="card_uid">
                                    <?php echo $card_uidErr; ?>
                                </div>

                                <div class="form-group">
                                    <label>IC Number :</label>
                                    <input type="text" class="form-control" value="<?php echo $ic_number; ?>" name="ic_number">
                                    <?php echo $ic_numberErr; ?>
                                </div>

                                <div class="form-group">
                                    <label>Date-of-Birth :</label>
                                    <input type="date" class="form-control" value="<?php echo $dob; ?>" name="dob">  
                                </div>

                                <div class="form-group form-check form-check-inline">
                                    <label class="form-check-label">Gender :</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" <?php if($gender == "Male"){ echo "checked"; } ?> value="Male" selected>
                                    <label class="form-check-label">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" <?php if($gender == "Female"){ echo "checked"; } ?> value="Female">
                                    <label class="form-check-label">Female</label>
                                </div>

                                <br>

                                <button type="submit" class="btn btn-primary btn-block">Edit</button>
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
