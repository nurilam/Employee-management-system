<?php 
    require_once "include/header.php";
?>

<?php 
    //  database connection
    require_once "../connection.php";

    $sql = "SELECT * FROM employee";
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
    color: #555;
    font-size: 18px;
}
</style>

<div class="container bg-white shadow">
    <div class="py-4 mt-5"> 
        <div class='text-center pb-2'><h4>Manage Staff</h4></div>
        <table style="width:100%" class="table-hover text-center ">
            <tr style="background-color: #ab4e52; color: black;">
                <th>S.No.</th>
                <th>Staff Id</th>
                <th>Name</th>
                <th>IC Number</th>
                <th>Email</th> 
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Salary in MYR</th>
                <th>Password</th>
                <th>Action</th>
            </tr>
            <?php 
            
            if( mysqli_num_rows($result) > 0){
                while( $rows = mysqli_fetch_assoc($result) ){
                    $name= $rows["name"];
                    $ic_number= $rows["ic_number"];
                    $email= $rows["email"];
                    $dob = $rows["dob"];
                    $gender = $rows["gender"];
                    $id = $rows["id"];
                    $salary = $rows["salary"];
                    $password = $rows["password"]; // Assuming 'password' is stored here

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
                        $age = $diff->format("%Y"); 
                    }

                    if($salary== "" ){
                        $salary= "Not Defined";
                    }   
                    
                    ?>
                    <tr>
                        <td><?php echo "{$i}."; ?></td>
                        <td><?php echo $id; ?></td>
                        <td> <?php echo $name ; ?></td>
                        <td><?php echo $ic_number; ?></td>
                        <td><?php echo $email; ?></td>
                        <td><?php echo $gender; ?></td>
                        <td><?php echo $dob; ?></td>
                        <td><?php echo $salary; ?></td>
                        
                        <!-- Password field with eye icon -->
                        <td>
                            <input type="password" class="form-control password-field" value="<?php echo $password; ?>" id="password<?php echo $i; ?>" readonly>
                            <span class="eye-icon" id="eye-icon<?php echo $i; ?>" onclick="togglePassword(<?php echo $i; ?>)">&#128065;</span>
                        </td>

                        <td>
                            <?php 
                                $edit_icon = "<a href='edit-employee.php?id= {$id}' class='btn-sm btn-primary float-right ml-3' style='background-color: green;'> <span ><i class='fa fa-edit '></i></span> </a>";
                                $delete_icon = " <a href='delete-employee.php?id={$id}' id='bin' class='btn-sm btn-primary float-right' style='background-color: #ae0c00;'> <span ><i class='fa fa-trash '></i></span> </a>";
                                echo $edit_icon . $delete_icon;
                            ?> 
                        </td>
                    </tr>

            <?php 
                    $i++;
                }
            } else {
                echo "<script>
                $(document).ready( function(){
                    $('#showModal').modal('show');
                    $('#linkBtn').attr('href', 'add-employee.php');
                    $('#linkBtn').text('Add Employee');
                    $('#addMsg').text('No Employees Found!');
                    $('#closeBtn').text('Remind Me Later!');
                })
             </script>
             ";
            }
            ?>
        </table>
    </div>
</div>

<script>
    // JavaScript function to toggle password visibility
    function togglePassword(index) {
        var passwordField = document.getElementById('password' + index);
        var eyeIcon = document.getElementById("eye-" + index);
        
        // Toggle password visibility
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
