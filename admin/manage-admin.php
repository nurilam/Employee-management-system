<?php 
    require_once "include/header.php";
?>

<?php 
//  database connection
require_once "../connection.php";

$sql = "SELECT * FROM admin";
$result = mysqli_query($conn , $sql);

$i = 1;
$you = "";
?>

<style>
table, th, td {
  border: 1px solid black;
  padding: 15px;
}
table {
  border-spacing: 10px;
}
.eye-icon {
  cursor: pointer;
  color: #007bff;
}
</style>

<div class="container bg-white shadow">
    <div class="py-4 mt-5"> 
    <div class='text-center pb-2'><h4>Manage Admin</h4></div>
    <table style="width:100%" class="table-hover text-center ">
    <tr style="background-color: #ab4e52; color: black;">
        <th>S.No.</th>
        <th>Name</th>
        <th>Email</th> 
        <th>Gender</th>
        <th>Date of Birth</th>
        <th>Age</th>
        <th>Password</th> <!-- New column for Password -->
        <th>Action</th>
    </tr>
    <?php 
    if( mysqli_num_rows($result) > 0){
        while( $rows = mysqli_fetch_assoc($result) ){
            $name= $rows["name"];
            $email= $rows["email"];
            $dob = $rows["dob"];
            $gender = $rows["gender"];
            $password = $rows["password"]; // Assuming you have a password field
            $id = $rows["id"];
            
            if($gender == "" ){
                $gender = "Not Defined";
            } 

            if($dob == "" ){
                $dob = "Not Defined";
                $age = "Not Defined";
            }else{
                $dob = date('jS F, Y' , strtotime($dob));
                $date1=date_create($dob);
                $date2=date_create("now");
                $diff=date_diff($date1,$date2);
                $age = $diff->format("%Y Years"); 
            }
           
            ?>
        <tr>
        <td><?php echo $i; ?></td>
        <td> <?php echo $name ; ?></td>
        <td><?php echo $email; ?></td>
        <td><?php echo $gender; ?></td>
        <td><?php echo $dob; ?></td>
        <td><?php echo $age; ?></td>

        <!-- Password field with eye icon -->
        <td>
            <input type="password" id="password-<?php echo $id; ?>" value="<?php echo $password; ?>" disabled style="width: 150px;">
            <i id="eye-<?php echo $id; ?>" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility(<?php echo $id; ?>)"></i>
        </td>

        <td>   
            <?php 
            if( $email !== $_SESSION["email"] ){
                $edit_icon = "<a href='edit-admin.php?id= {$id}' class='btn-sm btn-primary float-right ml-3 ' style='background-color: green;'> <span ><i class='fa fa-edit '></i></span> </a>";
                $delete_icon = " <a href='delete-admin.php?id={$id}' id='bin' class='btn-sm btn-primary float-right' style='background-color: #ae0c00;'> <span ><i class='fa fa-trash '></i></span> </a>";
                echo $edit_icon . $delete_icon;
            } else{
                echo "<a href='profile.php' class='btn btn-primary float-right' style='background-color: #0047ab;'>Profile</a>";
            } ?> 
        </td>
        </tr>

    <?php 
            $i++;
            }
        }else{
        echo "no admin found";
        }
    ?>
     </table>
    </div>
</div>

<!-- JavaScript to toggle password visibility -->
<script>
function togglePasswordVisibility(id) {
    var passwordField = document.getElementById("password-" + id);
    var eyeIcon = document.getElementById("eye-" + id);
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}
</script>

<?php 
    require_once "include/footer.php";
?>
