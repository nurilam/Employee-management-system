<?php 
require_once "include/header.php";
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>

<script>
    function validatePassword() {
        var new_pass = document.getElementById("new_pass").value;
        var confirm_pass = document.getElementById("confirm_pass").value;
        var errorMessage = "";

        // Password rules: at least 8 characters, one uppercase, one lowercase, one number, and one symbol
        var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!passwordPattern.test(new_pass)) {
            errorMessage = "Password must be at least 8 characters long and contain an uppercase letter, a lowercase letter, a number, and a symbol.";
        } else if (new_pass !== confirm_pass) {
            errorMessage = "Passwords do not match.";
        }

        if (errorMessage) {
            document.getElementById("passwordError").innerHTML = "<p style='color:red'>" + errorMessage + "</p>";
            return false;
        }
        return true;
    }
</script>

<?php 
    $old_passErr = $new_passErr = $confirm_passErr = "";
    $old_pass = $new_pass = $confirm_pass = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (empty($_REQUEST["old_pass"])) {
            $old_passErr = " <p style='color:red'>* Old Password Is required </p>";
        } else {
            $old_pass = trim($_REQUEST["old_pass"]);
        }

        if (empty($_REQUEST["new_pass"])) {
            $new_passErr = " <p style='color:red'>* New Password Is required </p>";
        } else {
            $new_pass = trim($_REQUEST["new_pass"]);
        }

        if (empty($_REQUEST["confirm_pass"])) {
            $confirm_passErr = " <p style='color:red'>* Please Confirm new Password! </p>";
        } else {
            $confirm_pass = trim($_REQUEST["confirm_pass"]);
        }

        // Server-side validation for the password format
        if (!empty($old_pass) && !empty($new_pass) && !empty($confirm_pass)) {
            // Validate new password rules: 8 characters, uppercase, lowercase, number, symbol
            if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $new_pass)) {
                $new_passErr = " <p style='color:red'>* Password must be at least 8 characters, include an uppercase letter, lowercase letter, number, and symbol. </p>";
            } elseif ($new_pass !== $confirm_pass) {
                $confirm_passErr = " <p style='color:red'>* Confirmed password does not match the new password </p>";
            } else {
                require_once "../connection.php";

                $check_old_pass = "SELECT password FROM employee WHERE email = '$_SESSION[email_emp]' && password = '$old_pass'";
                $result = mysqli_query($conn, $check_old_pass);

                if (mysqli_num_rows($result) > 0) {
                    $change_pass_query = "UPDATE employee SET password = '$new_pass' WHERE email = '$_SESSION[email_emp]'";
                    if (mysqli_query($conn, $change_pass_query)) {
                        session_unset();
                        session_destroy();
                        echo "<script>
                        $(document).ready(function() {
                            $('#addMsg').text('Password Updated successfully! Log in With New Password');
                            $('#linkBtn').attr('href', 'login.php');
                            $('#linkBtn').text('OK, Understood');
                            $('#modalHead').hide();
                            $('#closeBtn').hide();
                            $('#showModal').modal('show');
                        });
                        </script>";
                    }
                } else {
                    $old_passErr = " <p style='color:red'>*Sorry! Old Password is Wrong </p>";
                }
            }
        }
    }
?>

<div style="margin-top:100px"> 
    <div class="login-form-bg h-100">
        <div class="container mt-5 h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5 shadow">                       
                                <h4 class="text-center">Change Password</h4>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return validatePassword()">
                                    <div class="form-group">
                                        <label>Old Password:</label>
                                        <input type="password" name="old_pass" class="form-control">
                                        <?php echo $old_passErr; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>New Password:</label>
                                        <input type="password" name="new_pass" class="form-control" id="new_pass">
                                        <?php echo $new_passErr; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password:</label>
                                        <input type="password" name="confirm_pass" class="form-control" id="confirm_pass">
                                        <?php echo $confirm_passErr; ?>
                                        <div id="passwordError"></div>
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
