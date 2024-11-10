<?php 
require_once "include/header.php";
?>
<?php

        // database connection
        require_once "../connection.php";

         $currentDay = date( 'Y-m-d', strtotime("today") );
         $tomarrow = date( 'Y-m-d', strtotime("+1 day") );

         $today_leave = 0;
         $tomarrow_leave = 0;
         $this_week = 0;
         $next_week = 0;
            $i = 1;
        // total admin
        $select_admins = "SELECT * FROM admin";
        $total_admins = mysqli_query($conn , $select_admins);

        // total employee
        $select_emp = "SELECT * FROM employee";
        $total_emp = mysqli_query($conn , $select_emp);

        // employee on leave
        $emp_leave  ="SELECT * FROM emp_leave";
        $total_leaves = mysqli_query($conn , $emp_leave);

        if( mysqli_num_rows($total_leaves) > 0 ){
            while( $leave = mysqli_fetch_assoc($total_leaves) ){
                $leave = $leave["start_date"];

                //daywise
                if($currentDay == $leave){
                    $today_leave += 1;
                }elseif($tomarrow == $leave){
                   $tomarrow_leave += 1;
                }


            }
        }else {
            echo "no leave found";
        }


        // highest paid employee
        $sql_highest_salary =  "SELECT * FROM employee ORDER BY salary DESC";
        $emp_ = mysqli_query($conn , $sql_highest_salary);



?>

<div class="container">

    <div class="row mt-5">
    <div class="col-4">
            <div class="card shadow" style="width: 18rem;">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center" style="background-color: #7b1113; color: white;"><b>Admins</b></li> <!-- Fixed the closing quote -->
                    <li class="list-group-item">Total Admin : <?php echo mysqli_num_rows($total_admins); ?> </li>
                    <li class="list-group-item text-center"><a href="manage-admin.php" style="color: black;"><b>View All Admins</b></a></li>
                </ul>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow " style="width: 18rem;">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center" style="background-color: #7b1113; color: white;"><b>Staff</b></li>
                    <li class="list-group-item">Total Staff : <?php echo mysqli_num_rows($total_emp); ?></li>
                    <li class="list-group-item text-center"><a href="manage-employee.php" style="color: black;"> <b>View All Staff</b></a></li>
                </ul>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow " style="width: 18rem;">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center" style="background-color: #7b1113; color: white;"><b>Staff on  Leave</b> (Daywise)</li>
                    <li class="list-group-item" style="color: black;">Today :  <?php echo $today_leave; ?>  </li>
                    <li class="list-group-item" style="color: black;">Tomarrow :  <?php echo $tomarrow_leave; ?> </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row mt-5 bg-white shadow "> 
    <div class="col-12">
            <div class=" text-center my-3 "> <h4>Staff Leadership Board</h4> </div>
            <table class="table  table-hover">
            <thead>
                <tr style="background-color: #ab4e52; color: black;">
                    <th scope="col">S.No.</th>
                    <th scope="col">Staff's Id</th>
                    <th scope="col">Staff's Name</th>
                    <th scope="col">Staff's Email</th>
                    <th scope="col">Salary in MYR</th>
                </tr>
            </thead>
        <tbody>
        <?php while( $emp_info = mysqli_fetch_assoc($emp_) ){
                    $emp_id = $emp_info["id"];
                    $emp_name = $emp_info["name"];
                    $emp_email = $emp_info["email"];
                    $emp_salary = $emp_info["salary"];
                    ?>
            <tr>
            <th ><?php echo "$i. "; ?></th>
            <th ><?php echo $emp_id; ?></th>
            <td><?php echo $emp_name; ?></td>
            <td><?php echo $emp_email; ?></td>
            <td><?php echo $emp_salary; ?></td>
            </tr>

          <?php  
          $i++; 
                } 
            ?>
        </tbody>
        </table>
    </div>
    </div>
</div>

<?php 
require_once "include/footer.php";
?>