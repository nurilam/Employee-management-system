<?php 
require_once "include/header.php";
?>
<?php

        // database connection
        require_once "../connection.php";

         
        $i = 1;
        


        // applied leaves--------------------------------------------------------------------------------------------
        $total_accepted = $total_pending = $total_canceled = $total_applied = 0;
        $leave = "SELECT * FROM emp_leave WHERE email = '$_SESSION[email_emp]' ";
        $result = mysqli_query($conn , $leave);

        if( mysqli_num_rows($result) > 0 ){

            $total_applied = mysqli_num_rows($result);

            while( $leave_info = mysqli_fetch_assoc($result) ){
                $status = $leave_info["status"];

                if( $status == "pending" ){
                    $total_pending += 1;
                }elseif( $status == "Accepted" ){
                    $total_accepted += 1;
                }elseif( $status = "Canceled"){
                    $total_canceled += 1;
                }
            }
        }else{
            $total_accepted = $total_pending = $total_canceled = $total_applied = 0;
        }



        // leave status--------------------------------------------------------------------------------------------------------------
        $currentDay = date( 'Y-m-d', strtotime("today") );

        $last_leave_status = "No leave appliyed";
        $upcoming_leave_status = "";

        // for last leave status
        $check_leave = "SELECT * FROM emp_leave WHERE email = '$_SESSION[email_emp]' ";
        $s = mysqli_query($conn , $check_leave);
        if( mysqli_num_rows($s) > 0 ){
            while( $info = mysqli_fetch_assoc($s) ){
               $last_leave_status =  $info["status"] ;
            }
    }


    // for next leave date
    $check_ = "SELECT * FROM emp_leave WHERE email = '$_SESSION[email_emp]' ORDER BY start_date ASC ";
    $e = mysqli_query($conn , $check_); 
    if( mysqli_num_rows($e) > 0 ){
        while( $info = mysqli_fetch_assoc($e) ){
            $date = $info["start_date"] ;
            $last_leave =  $info["status"] ;
           if ( $date > $currentDay && $last_leave == "Accepted" ){
               $upcoming_leave_status = date('jS F', strtotime($date) ) ;
               break;
           }
        }
}


        // total employee--------------------------------------------------------------------------------------------
        $select_emp = "SELECT * FROM employee";
        $total_emp = mysqli_query($conn , $select_emp);

       



        // highest paid employee--------------------------------------------------------------------------
        $sql_highest_salary =  "SELECT * FROM employee ORDER BY salary DESC";
        $emp_ = mysqli_query($conn , $sql_highest_salary);



?>

<div class="container">

    <div class="row mt-5">
        <div class="col-4">
            <div class="card shadow " style="width: 18rem;">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center" style="background-color: #7b1113; color: white;"> <b>Leave Status</b> </li>
                    <li class="list-group-item"><b>Upcoming Leave on :</b>  <?php echo  $upcoming_leave_status ; ?>  </li>
                    <li class="list-group-item"><b>Last Leave's Status :</b>  <?php echo ucwords($last_leave_status) ;  ?> </li>
                </ul>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow " style="width: 18rem;">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center" style="background-color: #7b1113; color: white;"> <b>Applied leaves</b> </li>
                    <li class="list-group-item"><b>Total Accepted :</b> <?php echo $total_accepted;  ?> </li>
                    <li class="list-group-item"><b>Total Canceled :</b> <?php echo $total_canceled; ?> </li>
                    <li class="list-group-item"><b>Total Pending  : </b><?php echo $total_pending; ?> </li>
                    <li class="list-group-item"><b>Total Applied  : </b><?php echo $total_applied; ?> </li>
                </ul>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow " style="width: 18rem;">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center" style="background-color: #7b1113; color: white;"> <b>Staff</b>  </li>
                    <li class="list-group-item">Total Staff : <?php echo mysqli_num_rows($total_emp); ?></li>
                    <li class="list-group-item text-center"><a href="view-employee.php" style="color: black;"> <b>View All Staff</b></a></li>
                </ul>
            </div>
        </div>
    </div>
    </div>

<?php 
require_once "include/footer.php";
?>