<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link href="../resorce/css/style.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&family=Roboto:wght@400&display=swap" rel="stylesheet">

    <title>Employee Management System</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif; /* Change default font */
        }

        .bg {
            
            background-color: #d3d3d3;
            height: 100%; 
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .login-form-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%; /* Adjust width as needed */
            background-color: rgba(255, 255, 255, 0.9); /* White background with slight transparency */
          padding: 30px; /* Add padding for better spacing */
          border-radius: 10px; /* Rounded corners */
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .card {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background */
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background-color: maroon; /* Main button color */
            border-color: maroon;
        }

        .btn-primary:hover {
            background-color: darkred; /* Darker shade on hover */
            border-color: darkred;
        }

        .login-form__footer {
            text-align: center;
            margin-top: 10px;
            color: maroon;
        }

        .text-danger {
            color: red;
        }

        h4 {
            color: maroon; /* Heading color */
            font-size: 2.5rem; /* Increased font size */
            font-family: 'Montserrat', sans-serif; /* Changed font family */
            font-weight: 600; /* Semi-bold */
        }
    </style>
</head>
<body>

<?php 
$email_err = $pass_err = $login_Err = "";
$email = $pass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_REQUEST["email"])) {
        $email_err = "<p class='text-danger'>* Email cannot be empty</p>";
    } else {
        $email = $_REQUEST["email"];
    }

    if (empty($_REQUEST["password"])) {
        $pass_err = "<p class='text-danger'>* Password cannot be empty</p>";
    } else {
        $pass = $_REQUEST["password"];
    }

    if (!empty($email) && !empty($pass)) {
        // Database connection
        require_once "../connection.php";

        $sql_query = "SELECT * FROM admin WHERE email='$email' AND password='$pass'";
        $result = mysqli_query($conn, $sql_query);

        if (mysqli_num_rows($result) > 0) {
            while ($rows = mysqli_fetch_assoc($result)) {
                session_start();
                session_unset();
                $_SESSION["email"] = $rows["email"];
                header("Location: dashboard.php?login-success");
            }
        } else {
            $login_Err = "<div class='alert alert-warning alert-dismissible fade show'>
                <strong>Invalid Email/Password</strong>
                <button type='button' class='close' data-dismiss='alert'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>";
        }
    }
}
?>

<div class="bg">
    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5 shadow">
                                <h4 class="text-center">Hello, Admin</h4>
                                <div class="text-center my-3"><?php echo $login_Err; ?></div>

                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                    <div class="form-group">
                                        <label>Email:</label>
                                        <input type="email" class="form-control" value="<?php echo $email; ?>" name="email">
                                        <?php echo $email_err; ?>
                                    </div>

                                    <div class="form-group">
                                        <label>Password:</label>
                                        <input type="password" class="form-control" name="password">
                                        <?php echo $pass_err; ?>
                                    </div>

                                    <div class="form-group">
                                        <input type="submit" value="Log-In" class="btn btn-primary btn-lg w-100">
                                    </div>
                                    <p class="login-form__footer">Not an admin? <a href="../employee/login.php" class="text-primary">Log-In</a> as Staff now</p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script src="./resorce/plugins/common/common.min.js"></script>
<script src="./resorce/js/custom.min.js"></script>
<script src="./resorce/js/settings.js"></script>
<script src="./resorce/js/gleek.js"></script>
<script src="./resorce/js/styleSwitcher.js"></script>
</body>
</html>
