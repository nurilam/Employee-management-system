<?php 
require_once "include/header.php";
?> 

<?php  
// Database connection
require_once "../connection.php";

$sql_command = "SELECT * FROM employee WHERE email = '$_SESSION[email_emp]' ";
$result = mysqli_query($conn, $sql_command);

if (mysqli_num_rows($result) > 0) {
    while ($rows = mysqli_fetch_assoc($result)) {
        $name = ucwords($rows["name"]);
        $gender = ucwords($rows["gender"]);
        $dob = $rows["dob"];
        $salary = $rows["salary"];
        $card_uid = $rows["card_uid"]; // Fetching card UID
        $ic_number = $rows["ic_number"]; // Fetching IC number
        $dp = $rows["dp"];     
        $id = $rows["id"];
    }

    // Handling empty values for gender and dob
    if (empty($gender)) {
        $gender = "Not Defined";
    }
    
    if (!empty($dob)) {
        $dob = date('jS F Y', strtotime($dob));
    } else {
        $dob = "Not Defined";
        $age = "Not Defined";
    }

    // Calculate age if DOB is defined
    if ($dob !== "Not Defined") {
        $date1 = date_create($dob);
        $date2 = date_create("now");
        $diff = date_diff($date1, $date2);
        $age = $diff->format("%y Years");
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded">
                <div class="card-header text-center bg-info text-white">
                    <h3><?php echo $name; ?></h3>
                </div>
                <div class="text-center mt-4">
                    <img src="upload/<?php echo !empty($dp) ? $dp : '1.jpg'; ?>" class="rounded-circle" style="width: 150px; height: 150px; border: 5px solid white;" alt="Profile Photo">
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title">Employee Details</h5>
                    <p class="card-text"><strong>Email:</strong> <?php echo $_SESSION["email_emp"]; ?></p>
                    <p class="card-text"><strong>Employee ID:</strong> <?php echo $id; ?></p>
                    <p class="card-text"><strong>Gender:</strong> <?php echo $gender; ?></p>
                    <p class="card-text"><strong>Age:</strong> <?php echo isset($age) ? $age : "Not Defined"; ?></p>
                    <p class="card-text"><strong>Date of Birth:</strong> <?php echo $dob; ?></p>
                    <p class="card-text"><strong>Salary:</strong> MYR <?php echo number_format($salary, 2); ?></p>
                    <p class="card-text"><strong>Card UID:</strong> <?php echo $card_uid; ?></p>
                    <p class="card-text"><strong>IC Number:</strong> <?php echo $ic_number; ?></p>
                </div>
                <div class="card-footer text-center">
                    <a href="edit-profile.php" class="btn btn-outline-info mx-1">Edit Profile</a>
                    <a href="change-password.php" class="btn btn-outline-info mx-1">Change Password</a>
                    <a href="profile-photo.php" class="btn btn-outline-info mx-1">Change Profile Photo</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once "include/footer.php";
?>
