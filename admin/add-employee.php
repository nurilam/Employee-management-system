<?php 
    require_once "include/header.php";
?>

<?php  
    $nameErr = $emailErr = $passErr = $salaryErr = $cardUidErr = "";
    $name = $email = $dob = $gender = $pass = $salary = $card_uid = "";

    if( $_SERVER["REQUEST_METHOD"] == "POST" ){
        // Handling Gender and Date of Birth (same as before)
        if( empty($_REQUEST["gender"]) ){
            $gender =""; 
        } else {
            $gender = $_REQUEST["gender"];
        }

        if( empty($_REQUEST["dob"]) ){
            $dob = "";
        } else {
            $dob = $_REQUEST["dob"];
        }

        // Validating Name
        if( empty($_REQUEST["name"]) ){
            $nameErr = "<p style='color:red'> * Name is required</p>";
        } else {
            $name = $_REQUEST["name"];
        }

        // Validating Salary
        if( empty($_REQUEST["salary"]) ){
            $salaryErr = "<p style='color:red'> * Salary is required</p>";
            $salary = "";
        } else {
            $salary = $_REQUEST["salary"];
        }

        // Validating Email
        if( empty($_REQUEST["email"]) ){
            $emailErr = "<p style='color:red'> * Email is required</p> ";
        } else {
            $email = $_REQUEST["email"];
        }

        // Validating Password
        if( empty($_REQUEST["pass"]) ){
            $passErr = "<p style='color:red'> * Password is required</p> ";
        } else {
            $pass = $_REQUEST["pass"];
        }

        // Validating Card UID
        if( empty($_REQUEST["card_uid"]) ){
            $cardUidErr = "<p style='color:red'> * Card UID is required</p>";
            $card_uid = "";
        } else {
            $card_uid = $_REQUEST["card_uid"];
        }

        // Password validation regex
        if( !empty($pass) && !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $pass) ){
            $passErr = "<p style='color:red'> * Password must be at least 8 characters, contain one uppercase letter, one lowercase letter, one number, and one symbol.</p>";
        }

        // Check if everything is valid
        if( !empty($name) && !empty($email) && !empty($pass) && !empty($salary) && !empty($card_uid) ){

            // Database connection
            require_once "../connection.php";

            // Check if email already exists
            $sql_select_query = "SELECT email FROM employee WHERE email = '$email' ";
            $r = mysqli_query($conn , $sql_select_query);

            if( mysqli_num_rows($r) > 0 ){
                $emailErr = "<p style='color:red'> * Email Already Register</p>";
            } else {
                // Insert data including card_uid
                $sql = "INSERT INTO employee( name, email, password, dob, gender, salary, card_uid ) VALUES( '$name', '$email', '$pass', '$dob', '$gender', '$salary', '$card_uid' )";

                $result = mysqli_query($conn , $sql);
                if($result){
                    // Reset form fields
                    $name = $email = $dob = $gender = $pass = $salary = $card_uid = "";

                    // Success message
                    echo "<script>
                        $(document).ready(function(){
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
?>


<div style=""> 
<div class="login-form-bg h-100">
        <div class="container  h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-4 shadow">                       
                                    <h4 class="text-center">Add New Employee</h4>
                                <form method="POST" action=" <?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?> ">
                            
                                <div class="form-group">
                                    <label >Full Name :</label>
                                    <input type="text" class="form-control" value="<?php echo $name; ?>"  name="name" >
                                   <?php echo $nameErr; ?>
                                </div>

                                <div class="form-group">
                                    <label >Email :</label>
                                    <input type="email" class="form-control" value="<?php echo $email; ?>"  name="email" >     
                                    <?php echo $emailErr; ?>
                                </div>

                                <div class="form-group">
                                    <label >Password: </label>
                                    <input type="password" class="form-control" value="<?php echo $pass; ?>" name="pass" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character."> 
                                    <?php echo $passErr; ?>           
                                </div>

                                <div class="form-group">
                                    <label >Salary :</label>
                                    <input type="number" class="form-control" value="<?php echo $salary; ?>" name="salary" >  
                                    <?php echo $salaryErr; ?>            
                                </div>

                                <div class="form-group">
                                    <label>Card UID:</label>
                                    <input type="text" class="form-control" value="<?php echo $card_uid; ?>" name="card_uid" placeholder="Enter Card UID" >
                                </div>


                                <div class="form-group">
                                    <label >Date-of-Birth :</label>
                                    <input type="date" class="form-control" value="<?php echo $dob; ?>" name="dob" >  
                                </div>

                                <div class="form-group form-check form-check-inline">
                                    <label class="form-check-label" >Gender :</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" <?php if($gender == "Male" ){ echo "checked"; } ?>  value="Male"  selected>
                                    <label class="form-check-label" >Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" <?php if($gender == "Female" ){ echo "checked"; } ?>  value="Female">
                                    <label class="form-check-label" >Female</label>
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

