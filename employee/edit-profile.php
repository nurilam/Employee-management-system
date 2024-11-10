<?php
require_once "include/header.php";
?>

<?php  
// Database connection
require_once "../connection.php";

$session_email = $_SESSION["email_emp"];
$sql = "SELECT * FROM employee WHERE email= '$session_email' ";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($rows = mysqli_fetch_assoc($result)) {
        $name = $rows["name"];
        $email = $rows["email"];
        $dob = $rows["dob"];
        $gender = $rows["gender"];
        $ic_number = $rows["ic_number"]; // Fetch IC number
    }
}

$nameErr = $emailErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_REQUEST["gender"])) {
        $gender = "";
    } else {
        $gender = $_REQUEST["gender"];
    }

    if (empty($_REQUEST["dob"])) {
        $dob = "";
    } else {
        $dob = $_REQUEST["dob"];
    }

    if (empty($_REQUEST["name"])) {
        $nameErr = "<p style='color:red'> * Name is required</p>";
        $name = "";
    } else {
        $name = $_REQUEST["name"];
    }

    if (empty($_REQUEST["email"])) {
        $emailErr = "<p style='color:red'> * Email is required</p>";
        $email = "";
    } else {
        $email = $_REQUEST["email"];
    }

    // Fetch IC number from the form
    if (empty($_REQUEST["ic_number"])) {
        $ic_number = "";
    } else {
        $ic_number = $_REQUEST["ic_number"];
    }

    // If the name and email are provided
    if (!empty($name) && !empty($email)) {
        // Database connection
        require_once "../connection.php";

        // Check if the new email is different from the session email
        if ($email != $_SESSION["email_emp"]) {
            // Only check for duplicate emails if it's different
            $sql_select_query = "SELECT email FROM employee WHERE email = '$email'";
            $r = mysqli_query($conn, $sql_select_query);

            // Check if email already exists
            if (mysqli_num_rows($r) > 0) {
                $emailErr = "<p style='color:red'> * Email Already Registered</p>";
            } else {
                // Update the email, name, dob, gender, and ic_number
                $sql = "UPDATE employee SET name = '$name', email = '$email', dob = '$dob', gender = '$gender', ic_number = '$ic_number' WHERE email='$_SESSION[email_emp]'";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    $_SESSION['email_emp'] = $email; // Update session email
                    echo "<script>
                        $(document).ready(function(){
                            $('#showModal').modal('show');
                            $('#modalHead').hide();
                            $('#linkBtn').attr('href', 'profile.php');
                            $('#linkBtn').text('View Profile');
                            $('#addMsg').text('Profile Edited Successfully!!');
                            $('#closeBtn').hide();
                        });
                    </script>";
                }
            }
        } else {
            // If the email is the same as the current one, just update the other fields
            $sql = "UPDATE employee SET name = '$name', dob = '$dob', gender = '$gender', ic_number = '$ic_number' WHERE email='$_SESSION[email_emp]'";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo "<script>
                    $(document).ready(function(){
                        $('#showModal').modal('show');
                        $('#modalHead').hide();
                        $('#linkBtn').attr('href', 'profile.php');
                        $('#linkBtn').text('View Profile');
                        $('#addMsg').text('Profile Edited Successfully!!');
                        $('#closeBtn').hide();
                    });
                </script>";
            }
        }
    }
}
?>

<div>
    <div class="login-form-bg h-100">
        <div class="container mt-5 h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5 shadow">                       
                                <h4 class="text-center">Edit Your Profile</h4>
                                <form method="POST" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>">
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
                                        <label>Date of Birth:</label>
                                        <input type="date" class="form-control" value="<?php echo $dob; ?>" name="dob">  
                                    </div>

                                    <div class="form-group">
                                        <label>IC Number:</label>
                                        <input type="text" class="form-control" value="<?php echo $ic_number; ?>" name="ic_number">  
                                    </div>

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

                                    <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="Toolbar with button groups">
                                        <div class="btn-group">
                                            <input type="submit" value="Save Changes" class="btn btn-primary w-20" name="save_changes">        
                                        </div>
                                        <div class="input-group">
                                            <a href="profile.php" class="btn btn-primary w-20">Close</a>
                                        </div>
                                    </div>
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
