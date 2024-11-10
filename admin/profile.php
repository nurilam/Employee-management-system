<?php 
require_once "include/header.php";
?> 

<?php  
// Database connection
require_once "../connection.php";

$sql_command = "SELECT * FROM admin WHERE email = '$_SESSION[email]' ";
$result = mysqli_query($conn, $sql_command);

if (mysqli_num_rows($result) > 0) {
    while ($rows = mysqli_fetch_assoc($result)) {
        $name = ucwords($rows["name"]);
        $gender = ucwords($rows["gender"]);
        $dob = $rows["dob"];
        $dp = $rows["dp"];
    }

    // Handling empty values for gender and dob
    if (empty($gender)) {
        $gender = "Not Defined";
    }

    if (empty($dob)) {
        $dob = "Not Defined";
        $age = "Not Defined";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded">
                <div class="text-center mt-4">
                    <img src="upload/<?php echo !empty($dp) ? $dp : '1.jpg'; ?>" class="rounded-circle" style="width: 150px; height: 150px; border: 5px solid white;" alt="Profile Photo">
                </div>
                <div class="card-body text-center">
                    <h2 class="card-title mb-4"><?php echo $name; ?></h2>
                    <p class="card-text"><strong>Email:</strong> <?php echo $_SESSION["email"]; ?></p>
                    <p class="card-text"><strong>Gender:</strong> <?php echo $gender; ?></p>
                    <p class="card-text"><strong>Date of Birth:</strong> <?php echo $dob; ?></p>
                    <p class="card-text"><strong>Age:</strong> 
                        <?php 
                        if ($dob != "Not Defined") {  
                            $date1 = date_create($dob);
                            $date2 = date_create("now");
                            $diff = date_diff($date1, $date2);
                            echo $diff->format("%y Years"); 
                        }
                        ?> 
                    </p>
                </div>
                <div class="card-footer text-center">
                    <a href="edit-profile.php" class="btn btn-outline-info mx-1">Edit Profile</a>
                    <a href="change-password.php" class="btn btn-outline-info mx-1">Change Password</a>
                    <a href="profile-photo.php" class="mt-2 btn btn-outline-info mx-1">Change Profile Photo</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once "include/footer.php";
?>
